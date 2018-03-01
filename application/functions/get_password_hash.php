<?php

/**
 * Creates a password hash.
 *
 * @param string $password
 *
 * @return string
 */
function get_password_hash(string $password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}
