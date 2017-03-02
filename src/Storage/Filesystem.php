<?php
namespace Humphries\Storage;

use Humphries\Contracts\Filesystem as FilesystemInterface;
use League\Flysystem\Filesystem as Flysystem;

class Filesystem extends Flysystem implements FilesystemInterface
{

}