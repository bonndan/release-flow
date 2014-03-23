<?php
namespace bonndan\ReleaseFlow\Version\Detector;

use bonndan\ReleaseFlow\Version;

/**
 * Interface for classes which can detect the current version.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
interface DetectorInterface
{
    /**
     * Provides the current version.
     * 
     * @return Version
     */
    public function getCurrentVersion();
}
