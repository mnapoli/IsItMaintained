<?php

namespace Maintained\Storage;

/**
 * Storage.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Storage
{
    /**
     * Retrieve the data stored under the given ID.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function retrieve($id);

    /**
     * Store data under the given ID.
     *
     * @param string $id
     * @param mixed $data
     *
     * @return void
     */
    public function store($id, $data);
}
