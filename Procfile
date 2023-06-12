web: vendor/bin/heroku-php-apache2 public/
RewriteEngine On 
RewriteCond %{REQUEST_FILENAME} !-f 
RewriteCond %{REQUEST_FILENAME} !-d 
RewriteRule ^(.*)$ public/index.php [L]

AddType text/css .css