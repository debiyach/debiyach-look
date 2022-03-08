<?php
/**
 * @author Ömer Faruk GÖL <omerfarukgol@hotmail.com>
 */

namespace Debiyach\Look;


use Error;

class See
{

    private const DELIMITER = ".";

    /**
     * @var string
     */
    private string $viewPath;

    /**
     * @var string
     */
    private string $viewSuffix = "look";

    /**
     * @var string
     */
    private string $cachePath;

    /**
     * @var string
     */
    private string $view;

    public function __construct(string $viewPath, string $cachePath)
    {
        $this->setViewPath($viewPath);
        $this->setCachePath($cachePath);
    }

    /**
     * @param string $view
     * @param array $data
     * @return false|string
     * @author Ömer Faruk GÖL <omerfarukgol@hotmail.com>
     */
    public function render(string $view, array $data = [])
    {

        $viewFile = $this->makeViewFileName($view);

        if (!file_exists($viewFile)) {
            throw new Error(sprintf("View file not found: %s", $viewFile));
        }

        $this->view = file_get_contents($viewFile);
        $this->parse();
        $cacheFile = $this->makeCacheFileName($view);

        if (!file_exists($cacheFile)){
            file_put_contents($cacheFile,$this->view);
        }

        if (filemtime($cacheFile) < filemtime($viewFile)) {
            echo '<!-- cache yenilendi -->';
            file_put_contents($cacheFile, $this->view);
        }

        extract($data);
        ob_start();
        include $cacheFile;
        return ob_get_clean();
    }

    protected function parse(){
        $this->parseVariables();
        $this->parseForEach();
    }

    /**
     * Aşağıdaki direktifler için parse işlemi yapar
     * @foreach($array as $item)
     * @endforeach
     */
    public function parseForEach(): void
    {
        $this->view = preg_replace_callback('/@foreach\((.*?)\)/', function ($expression) {
            return '<?php foreach(' . $expression[1] . '): ?>';
        }, $this->view);

        $this->view = preg_replace('/@endforeach/', '<?php endforeach; ?>', $this->view);
    }

    protected function parseVariables(){
        $this->view = preg_replace_callback('/{{(.*?)}}/',function ($data){
            return '<?=' . trim($data[1]) . '?>';
        },$this->view);
    }

    protected function makeCacheFileName(string $view):string
    {
        return $this->cachePath . '/' . md5($view) . '.cache.php';
    }

    /**
     * Sınıfın erişebileceği türde dosya adını oluşturur.
     * @param string $filename
     * @return string
     * @author Ömer Faruk GÖL <omerfarukgol@hotmail.com>
     */
    protected function makeViewFileName(string $filename): string
    {
        return $this->viewPath . '/' . str_replace(self::DELIMITER, '/', $filename) . '.' . $this->viewSuffix . '.php';
    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * @param string $viewPath
     */
    public function setViewPath(string $viewPath): void
    {
        $this->viewPath = $viewPath;
    }

    /**
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    /**
     * @param string $cachePath
     */
    public function setCachePath(string $cachePath): void
    {
        $this->cachePath = $cachePath;
    }
}