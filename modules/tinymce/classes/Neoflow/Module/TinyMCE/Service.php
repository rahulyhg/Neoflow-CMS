<?php

namespace Neoflow\Module\TinyMCE;

use Neoflow\CMS\Core\AbstractView;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Module\WYSIWYG\Service as WysiwygService;

class Service extends WysiwygService
{
    /**
     * @var array
     */
    protected $options = [
        'theme' => 'modern',
        'menubar' => false,
        'plugins' => [
            'advlist autolink lists link image imagetools charmap print preview anchor textcolor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste',
        ],
        'toolbar' => 'insert | undo redo |  formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | searchreplace | visualblocks code',
        'content_css' => false,
        'image_upload' => true,
        'image_advtab' => true,
        'imagetools_toolbar' => 'editimage imageoptions',
        'insertdatetime_formats' => ['%H:%M:%S', '%Y-%m-%d', '%I:%M:%S %p', '%D', '%d.%m.%Y'],
        'mobile' => [
            'theme' => 'mobile',
            'plugins' => ['autosave', 'lists', 'autolink'],
            'toolbar' => ['undo', 'bold', 'italic', 'styleselect'],
        ],
    ];

    /**
     * @var ModuleModel
     */
    protected $module;

    /**
     * @var string
     */
    protected $templateFile = 'tinymce/editor';

    /**
     * Constructor.
     *
     * @param ModuleModel $module
     */
    public function __construct(ModuleModel $module)
    {
        $this->module = $module;

        $this->options['language'] = $this->translator()->getCurrentLanguageCode();

        $this->options['link_list'] = generate_url('lmod_tinymce_backend_api_pages');

        $frontendTheme = $this->settings()->getFrontendTheme();
        if (is_file($frontendTheme->getPath('/css/editor.css'))) {
            $this->options['content_css'] = $frontendTheme->getUrl('/css/editor.css');
        }
    }

    /**
     * Delete uploaded images.
     *
     * @param string $id
     *
     * @return bool
     */
    public function deleteUploadedImages($id): bool
    {
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg/'.$id);
        if (is_dir($mediaPath)) {
            return Folder::unlink($mediaPath, true);
        }

        return true;
    }

    /**
     * Upload file.
     *
     * @param string $dirPath Target directory path
     * @param string $dirUrl  Target directory url
     *
     * @return array
     */
    public function uploadFile(string $dirPath, string $dirUrl): array
    {
        $uploadedItem = $this->request()->getFile('file');

        $result = [
            'status' => false,
            'message' => '',
            'content' => '',
        ];

        try {
            $file = $this->service('upload')->move($uploadedItem, $dirPath, true, $this->settings()->getAllowedFileExtensions());

            $fileUrl = normalize_url($dirUrl.'/'.$file->getName());

            $result['status'] = true;
            $result['message'] = translate('Successfully uploaded');
            $result['content'] = $fileUrl;
            $result['file'] = [
                'name' => $file->getName(),
                'size' => $file->getSize(),
                'extension' => $file->getExtension(),
                'url' => $fileUrl,
            ];
        } catch (ValidationException $ex) {
            $result['message'] = $ex->getMessage();
        } catch (Exception $ex) {
            $result['message'] = translate('Upload file(s) failed, see error message').': '.$ex->getMessage();
        }

        return $result;
    }

    /**
     * Delete file.
     *
     * @param string $dirPath Target directory path
     *
     * @return array
     */
    public function deleteFile(string $dirPath): array
    {
        $fileName = str_replace('../', '', $this->request()->getGet('name'));

        $result = [
            'status' => false,
        ];

        $filePath = normalize_path($dirPath.'/'.$fileName);
        if (!is_file($filePath) || File::unlink($filePath)) {
            $result['status'] = true;
        }

        return $result;
    }

    /**
     * Get files.
     *
     * @param string $dirPath Target directory path
     * @param string $dirUrl  Target directory url
     *
     * @return array
     */
    public function getFiles(string $dirPath, string $dirUrl): array
    {
        $targetDirFolder = Folder::load($dirPath);

        $result = [];
        $files = $targetDirFolder->findFiles('*.*', GLOB_MARK | GLOB_BRACE);
        foreach ($files as $file) {
            $result[] = [
                'name' => $file->getName(),
                'url' => normalize_url($dirUrl.'/'.$file->getName()),
                'extension' => $file->getExtension(),
                'size' => $file->getSize(),
            ];
        }

        return $result;
    }

    /**
     * Create session key.
     *
     * @param string $id Editor id
     *
     * @return string
     */
    public function generateKey(string $id)
    {
        return md5('tinymce-'.$id);
    }

    /**
     * Render code editor.
     *
     * @param AbstractView $view    View
     * @param string       $name    Editor name (form control / textarea)
     * @param string       $id      Editor id (form control / textarea)
     * @param string       $content Editor content
     * @param string       $height  Editor height
     * @param array        $options Editor options
     *
     * @return string
     */
    public function renderEditor(AbstractView $view, string $name, string $id, string $content, string $height = '450px', array $options = []): string
    {
        $this->engine()->addJavascriptUrl($this->module->getUrl('statics/tinymce/tinymce.min.js'));

        $options = array_merge($this->options, ['height' => $height], $options);

        $key = $this->generateKey($id);

        $fileUploadUrl = generate_url('lmod_tinymce_backend_api_file_upload', [
            'key' => $key,
        ]);
        $filesUrl = generate_url('lmod_tinymce_backend_api_files', [
            'key' => $key,
        ]);
        if (!isset($options['uploadDirectory']['path']) || !isset($options['uploadDirectory']['url'])) {
            $options['image_upload'] = false;
        }
        $this->session()->set($key, $options);

        if ($options['image_upload']) {
            $options = array_merge($options, [
                'images_upload_url' => $fileUploadUrl,
                'images_reuse_filename' => true,
                'automatic_uploads' => true,
                'relative_urls' => false,
                'remove_script_host' => false,
                'document_base_url' => $this->config()->getUrl(),
                'file_picker_types' => 'file image media',
                'file_picker_callback' => 'function(callback, value, meta) {

                    var input = document.createElement("input");
                    input.setAttribute("type", "file");

                    if (meta.filetype == "image") {
                        input.setAttribute("accept", "image/*");
                    }

                    input.onchange = function() {

                        var file = this.files[0];

                        data = new FormData();
                        data.append("file", file);

                        $.ajax({
                            data: data,
                            type: "POST",
                            url: "'.$fileUploadUrl.'",
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                if (data.status) {
                                    if (meta.filetype == "file") {
                                        callback(data.content, {text: data.file.name});
                                    } else if (meta.filetype == "image") {
                                       callback(data.content, {alt: data.file.name});
                                    } else if (meta.filetype == "media") {
                                        callback(data.content);
                                    }
                                } else {
                                    tinymce.activeEditor.windowManager.alert(data.message);
                                }
                            },
                            error: function(xhr) {
                                tinymce.activeEditor.windowManager.alert("HTTP Error: " + xhr.status);
                            }
                        });

                    };

                    input.click();

                }',
                'images_upload_handler' => 'function(blobInfo, success, failure) {

                    data = new FormData();
                    data.append("file", blobInfo.blob(), blobInfo.filename());

                    $.ajax({
                        data: data,
                        type: "POST",
                        url: "'.$fileUploadUrl.'",
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            if (data.status) {
                                success(data.content);
                            } else {
                               failure(data.message);
                            }
                        },
                        error: function(xhr) {
                            failure("HTTP Error: " + xhr.status);
                        }
                    });
                }',
                'setup' => 'function(editor) {
                    editor.addSidebar("files", {
                        tooltip: "'.translate('Uploaded file', [], true).'",
                        icon: "browse",
                        onshow: function (api) {

                                var $apiElement = $(api.element()),
                                    $container = $("<div>", {class: "mce-container-body mce-window-body mce-abs-layout"});

                            $apiElement.empty();

                            $.get("'.$filesUrl.'", function(data) {

                                if (data[0] != undefined) {

                                    $.each(data, function(i, file) {

                                        var $row = $("<div>", {
                                            class: "row mce-menubar",
                                            style: "border-top: 0; border-right: 0; border-left: 0;"
                                        });

                                        var $fileColumn = $("<div>", {
                                                style: "padding: 10px 5px 10px 10px; width: 110px; text-align: right;"
                                            }),
                                            $actionColumn = $("<div>", {
                                                style: "padding: 10px 10px 10px 5px; width: 110px; text-algin: left;"
                                            });

                                        var $addLinkButton = $("<button>", {
                                            title: "'.translate('Add link').'",
                                            html: "<span class=\"mce-txt\"><i class=\"mce-ico mce-i-link\"></i></span>"
                                        })
                                            .on("click", function(e) {
                                                e.preventDefault();
                                                tinymce.activeEditor.execCommand("mceInsertContent", false, "<a href=\"" + file.url + "\" title=\"" + file.name + "\">" + file.name + "</a>");
                                            });

                                        var $addLinkButtonWrapper = $("<div>", {
                                            class: "mce-widget mce-btn",
                                            style: "margin-right: 4px;"
                                        })
                                            .append($addLinkButton);

                                        var $deleteButton = $("<button>", {
                                            title: "'.translate('Delete file').'",
                                            html: "<span class=\"mce-txt\"><i class=\"mce-ico mce-i-remove\"></i></span>"
                                        })
                                            .on("click", function(e) {
                                                e.preventDefault();
                                                tinymce.activeEditor.windowManager.confirm("'.translate('Are you sure you want to delete it?').'", function(status) {
                                                    if (status) {
                                                        $.get("'.generate_url('lmod_tinymce_backend_api_file_delete', ['key' => $key]).'&name=" + file.name, function(data) {
                                                            if (data.status) {
                                                                $fileColumn.remove();
                                                                $actionColumn.remove();

                                                                $content = $(tinymce.activeEditor.getContent());

                                                                $content.find("[src$=\"" + file.name + "\"]").remove();

                                                                var $anchor = $content.find("[href$=\"" + file.name + "\"]");
                                                                $anchor.replaceWith($anchor.text());

                                                                tinymce.activeEditor.setContent("");
                                                                if ($content.length) {
                                                                    tinymce.activeEditor.setContent($content[0].outerHTML);
                                                                }
                                                            }
                                                        });
                                                    }
                                                });
                                            });

                                        var $deleteButtonWrapper = $("<div>", {
                                            class: "mce-widget mce-btn"
                                        })
                                            .append($deleteButton);

                                        if ($.inArray(file.extension, ["gif","png","jpg","jpeg"]) !== -1) {

                                            var $img = $("<img />", {
                                                src: file.url,
                                                title: "'.translate('Add image').'",
                                                style: "max-width: 96%; max-height: 50px; line-height: 50px padding: 0 2%;"
                                            });

                                            $fileColumn.append($img);

                                            var $addImageButton = $("<button>", {
                                                title: "'.translate('Add image').'",
                                                html: "<span class=\"mce-txt\"><i class=\"mce-ico mce-i-image\"></i></span>"
                                            })
                                                .on("click", function(e) {
                                                    e.preventDefault();
                                                    tinymce.activeEditor.execCommand("mceInsertContent", false, "<img src=\"" + file.url + "\" alt=\"" + file.name+ "\" title=\"" + file.name+ "\" />");
                                                });

                                            var $addImageButtonWrapper = $("<div>", {
                                                class: "mce-widget mce-btn",
                                                style: "margin-right: 5px;"
                                            })
                                                .append($addImageButton);

                                            $actionColumn.append($addImageButtonWrapper, $addLinkButtonWrapper, $deleteButtonWrapper);

                                        } else {

                                            var $fileName = $("<span>", {
                                                text: file.name,
                                                class: "mce-widget mce-label",
                                                style: "font-size: 12px; text-align: right; overflow-wrap: break-word; word-break: break-all; word-break: break-word; hyphens: auto; white-space: normal;"
                                            });

                                            $fileColumn.append($fileName);

                                            $actionColumn.append($addLinkButtonWrapper, $deleteButtonWrapper);
                                        }

                                        $row.append($fileColumn, $actionColumn);

                                        $container.append($row);

                                    });

                                } else {
                                    $container.append("<p style=\"padding: 10px\">'.translate('No files found').'</p>");
                                }

                                $apiElement.append($container);

                            });
                        },

                    });
                }',
            ]);
        }

        $this->engine()->addJavascript('
            tinymce.init({
                selector: "textarea[name=\''.$name.'\']",
                '.convert_php2js($options, false).'
            });
        ');

        return parent::renderEditor($view, $name, $id, $content, $height, $options);
    }
}
