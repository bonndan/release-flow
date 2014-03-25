<?php
namespace bonndan\ReleaseFlow\Version;

use bonndan\ReleaseFlow\Version;
use bonndan\ReleaseFlow\Version\Detector\DetectorInterface;
use InvalidArgumentException;
use SplFileObject;


/**
 * Helper to read/manipulate the composer file.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @todo replace with library
 */
class ComposerFile implements DetectorInterface
{
    /**
     * composer file
     * 
     * @var SplFileObject
     */
    private $composerFile;

    /**
     * Constructor.
     * 
     * @param SplFileObject $file
     */
    public function __construct(SplFileObject $file = null)
    {
        if ($file !== null) {
            $this->setComposerFile($file);
        }
    }

    /**
     * Set the path to the composer file.
     * 
     * @param SplFileObject $file
     * @throws \Liip\RMT\Exception
     */
    public function setComposerFile(SplFileObject $file)
    {
        if (!$file->isReadable()) {
            throw new Exception("The composer file is not readable.");
        }
        $this->composerFile = $file;
    }
    
    /**
     * Returns the project name.
     * 
     * @return string|null
     */
    public function getProjectName()
    {
        $json = $this->getJson();
        if (!isset($json->name)) {
            return null;
        }
        
        return $json->name;
    }

    /**
     * Sets the new version
     * 
     * @param Version $version
     */
    public function setVersion(Version $version)
    {
        $json = $this->getJson();
        $json->version = $version->getVersion();
        return $this->save($json);
    }

    /**
     * Returns the current version as stored in the composer file.
     * 
     * @return string|null
     */
    public function getCurrentVersion()
    {
        $json = $this->getJson();
        if (!isset($json->version)) {
            return null;
        }
        
        return $json->version;
    }
    
     /**
     * Format a one line JSON string
     *  Picked from here: http://php.net/manual/en/function.json-encode.php#80339
     *
     * @param $json
     * @param string $tab
     * @return bool|string
     */
    public static function format($json, $tab = "   ")
    {
        $formatted = "";
        $indentLevel = 0;
        $inString = false;

        // Normalized
        $jsonObj = json_decode($json);
        if($jsonObj === null) {
            throw new InvalidArgumentException("Invalid JSON string");
        }
        $json = json_encode($jsonObj);

        // Format
        $len = strlen($json);
        for($c = 0; $c < $len; $c++) {
            $char = $json[$c];
            switch($char) {
                case '{':
                case '[':
                    if(!$inString) {
                        $formatted .= $char . "\n" . str_repeat($tab, $indentLevel+1);
                        $indentLevel++;
                    }
                    else {
                        $formatted .= $char;
                    }
                    break;
                case '}':
                case ']':
                    if(!$inString) {
                        $indentLevel--;
                        $formatted .= "\n" . str_repeat($tab, $indentLevel) . $char;
                    }
                    else {
                        $formatted .= $char;
                    }
                    break;
                case ',':
                    if(!$inString) {
                        $formatted .= ",\n" . str_repeat($tab, $indentLevel);
                    }
                    else {
                        $formatted .= $char;
                    }
                    break;
                case ':':
                    if(!$inString) {
                        $formatted .= ": ";
                    }
                    else {
                        $formatted .= $char;
                    }
                    break;
                case '"':
                    if($c > 0 && $json[$c-1] != '\\') {
                        $inString = !$inString;
                    }
                default:
                    $formatted .= $char;
                    break;
            }
        }

        return $formatted;
    }

    
    /**
     * Returns the decoded json.
     * 
     * @return object
     */
    private function getJson()
    {
        return json_decode(file_get_contents($this->composerFile->getPathname()));
    }
    
    /**
     * Saves the json object back to the composer file.
     * 
     * @param object $json
     * @return string the serialized content
     */
    private function save($json)
    {
        $serialized = self::format(json_encode($json));
        $fixed = str_replace('"_empty_":', '"":', $serialized);
        file_put_contents($this->composerFile->getPathname(), $fixed);
        return $serialized;
    }
}