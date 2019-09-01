<?php
/**
 * Created by PhpStorm.
 * Filename: ConsoleSecurityConfig.php
 * User: Andrei Gache
 * Email: andrei.gache.99@gmail.com
 * Website: https://www.andrei-gache.com/
 * Date: 01/09/19
 * Time: 20:20
 */

namespace App\Model;


class ConsoleSecurityConfig
{
    private $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

}