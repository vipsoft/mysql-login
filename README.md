# VIPSoft\MySQLLogin

Reader/decoder for `.mylogin.cnf` -- MySQL's obfuscated authentication credentials login path file

## Features

* Simple to use!

    ```php
    use VIPSoft\MySQLLogin;

    $reader = new MySQLLogin(get_env('HOME') . '/.mylogin.cnf');

    // get decoded credentials
    $credentials = $reader->get('client');

    $host = $credentials['host'];
    $user = $credentials['user'];
    $password = $credentials['password'];
    ```

## References

* [mysql_config_editor](https://dev.mysql.com/doc/refman/8.2/en/mysql-config-editor.html)

## Copyright

Copyright (c) 2023 Anthon Pang. See LICENSE for details.

## Contributors

* Anthon Pang [robocoder](http://github.com/robocoder)
