<?php

class Grafo 
{
    private $url_z1;
    private $resumePath;
    private $outputDir;

    public function __construct() 
    {
        $this->outputDir  = "./dist";
        $this->resumePath = $this->outputDir."/!README.md";
        $this->url_z1     = "/var/www/html/levier";
    }

    public function run() 
    {
        touch($this->resumePath);
        file_put_contents($this->resumePath, "| File | Lines |\n| ------ | ------ |\n", FILE_APPEND);

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
                    $lineCount = $this->countLinesInFile($path);
                    $this->add($file, $dir, $lineCount);
                }
            }
        }

        closedir($handle);
    }

    private function countLinesInFile(string $path)
    {
        $lineCount = 0;

        if (is_file($path)) {

            $fileHandle = fopen($path, "r");
    
            if ($fileHandle) {

                while (!feof($fileHandle)) {
                    fgets($fileHandle);
                    $lineCount++;
                }
    
                fclose($fileHandle);
            }
        }
    
        return $lineCount;
    }

    public function add(string $file, string $dir, string $lines) 
    {
        $fileName      = pathinfo($file, PATHINFO_FILENAME);
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $currentFile   = "$this->outputDir/$fileName.$fileExtension.md";

        # Count lines

        file_put_contents($this->resumePath, "| [[$fileName.$fileExtension]] | $lines |\n", FILE_APPEND);

        # Criando arquivo atual
        
        touch($currentFile);

        $pattern = '/\b(include|include_once|require|require_once)\s*\(?[\'"]([^\'"]+)[\'"]\)?/i';

        $fileContent = file_get_contents($dir . "/" . $file) . "\n";

        echo "Arquivo processado: $dir/$file\n";

        # verificando se o arquivo contém algum include / require
        
        if (preg_match_all($pattern, $fileContent, $matches)) {

            foreach ($matches[2] as $includedFile) {

                $includedFileName = basename($includedFile);
                echo "Include found: [$includedFileName]\n";

                # Incluindo arquivos que são vinculados no arquivo percorrido atual
                file_put_contents($currentFile, "[[$includedFileName]]\n", FILE_APPEND);
            }
        }
    }
}

$grafo = new Grafo();
$grafo->run();
