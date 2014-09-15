<?php

namespace Maintained\Storage;

/**
 * JSON file storage.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class JsonFileStorage implements Storage
{
    /**
     * @var string
     */
    private $directory;

    public function __construct($directory)
    {
        $this->directory = (string) $directory;
    }

    public function retrieve($id)
    {
        $filename = $this->getFilename($id);

        if (! file_exists($filename)) {
            return null;
        }

        $json = file_get_contents($filename);

        return json_decode($json);
    }

    public function store($id, $data)
    {
        $filename = $this->getFilename($id);

        $json = json_encode($data);

        file_put_contents($filename, $json);
    }

    private function getFilename($id)
    {
        return $this->directory . '/' . $id . '.json';
    }
}
