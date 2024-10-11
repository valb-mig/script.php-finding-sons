<?php
$url_z1 = "/var/www/html/levier";

# Entrar no diretório
# Não olhar a pasta vendor
# ler arquivo por arquivo dentro de pastas e fora que terminem com .inc ou .php

$dir = opendir($url_z1);

while($file = readdir($dir)) {

    if(!in_array($file, ['.', '..', 'vendor', '.git', 'composer'])) {

    echo "$file\n";
    (new Grafo)->add($file);

        // if(is_dir($url_z1."/".$file)) {
        //     $dir2 = opendir($url_z1."/".$file);
        // }
    }
}
          
class Grafo 
{
    public function add(string $file) 
    {
        mkdir("./dist/$file.md");
    }
}