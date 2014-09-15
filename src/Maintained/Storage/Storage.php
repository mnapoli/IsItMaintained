<?php

namespace Maintained\Storage;

/**
 * Storage.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Storage
{
    public function retrieve($id);
    public function store($id, $data);
}
