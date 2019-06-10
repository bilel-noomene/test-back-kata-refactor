<?php

namespace App\Helper;

use Symfony\Component\Finder\Finder;

/**
 * Search project classes (under src directory)
 */
class ClassFinder
{
    use SingletonTrait;

    /**
     * Find all classes under a namespace following the PSR 4 specification.
     *
     * @param string $namespace
     * @return array
     */
    public function findInNamespace($namespace = 'App')
    {
        $finder = new Finder();

        // The path of src folder
        $path = dirname(__DIR__);
        $classes = [];

        if (substr($namespace, 0, 3) !== 'App') {
            throw new \InvalidArgumentException('Namespace not supported');
        }

        $path .= str_replace('\\', '/', ltrim($namespace, 'App'));

        if (!is_dir($path)) {
            return $classes;
        }

        $finder->files()->in($path)->name('/\.php$/');

        foreach ($finder as $file) {
            $classes[] = $namespace . '\\' . str_replace('/', '\\', rtrim($file->getRelativePathname(), '.php'));
        }

        return $classes;
    }

    /**
     * Find classes  by interface and namespace.
     *
     * @param string $interface
     * @param string $namespace
     * @param bool|null $instantiable
     * @return array
     * @throws \ReflectionException
     */
    public function findByInterface($interface, $namespace = 'App', $instantiable = null)
    {
        $classes = $this->findInNamespace($namespace);
        $filteredClasses = [];

        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);

            if ($reflection->implementsInterface($interface) &&
                ($instantiable === null || $instantiable === $reflection->isInstantiable())) {
                $filteredClasses[] = $class;
            }
        }

        return $filteredClasses;
    }
}
