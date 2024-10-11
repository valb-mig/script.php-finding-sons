<?php

class Grafo 
{
    private $url_z1;

    public function __construct() 
    {
        $this->url_z1 = "/var/www/html/levier";
    }

    public function run() 
    {
        $this->scanDir($this->url_z1);
    }

    private function scanDir($dir) 
    {
        $handle = opendir($dir);

        while ($file = readdir($handle)) {

            if (!in_array($file, ['.', '..', 'vendor', '.git', 'composer'])) {
                
                $path = $dir . '/' . $file;

                if (is_dir($path)) {
                    $this->scanDir($path);
                }
                else if (preg_match('/\.(php|inc)$/', $file)) {
                    $this->add($file, $dir);
                }
            }
        }

        closedir($handle);
    }

    public function add(string $file, string $dir) 
    {
        $outputDir = "./dist";
        
        $fileName      = pathinfo($file, PATHINFO_FILENAME);
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $currentFile   = "$outputDir/$fileName.$fileExtension.md";

        /*
            digraph Grafo {
                normas -> "register_globals.php";
            }
        */

        # Criando arquivo atual
        touch($currentFile);

        $pattern = '/\b(include|include_once|require|require_once)\s*\(?[\'"]([^\'"]+)[\'"]\)?/i';

        $fileContent = file_get_contents($dir . "/" . $file) . "\n";

        echo "Arquivo processado: $dir/$file\n";

        # verificando se o arquivo contém algum include / require

        if (preg_match_all($pattern, $fileContent, $matches)) {

            foreach ($matches[2] as $includedFile) {

                $includedFileName = basename($includedFile);
                echo "Include encontrado: [$includedFileName]\n";

                # Incluindo arquivos que são vinculados no arquivo percorrido atual
                file_put_contents($currentFile, "[[$includedFileName]]\n", FILE_APPEND);
            }
        }
    }
}

$grafo = new Grafo();
$grafo->run();
