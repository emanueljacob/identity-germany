<?php

namespace Slashplus\Identity\Validation;

use Illuminate\Validation;
use Illuminate\Translation;
use Illuminate\Validation\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * Class ValidatorFactory
 *
 * @package Slashplus\Identity\Validation
 *
 * @method make(array $data, array $rules, array $messages = [], array $customAttributes = [])
 *
 * @see \Illuminate\Validation\Factory
 */
class ValidatorFactory
{
    /**
     * @var mixed|string
     */
    public $lang;

    /**
     * @var mixed|string
     */
    public $group;

    /**
     * @var Factory
     */

    public $factory;
    /**
     * @var mixed|string
     */
    public $namespace;

    /**
     * Translations root directory
     *
     * @var string
     */
    public $basePath;

    /**
     * @var
     */
    public static $translator;

    /**
     * ValidatorFactory constructor.
     *
     * @param string $namespace
     * @param string $lang
     * @param string $group
     */
    public function __construct($namespace = 'lang', $lang = 'en', $group = 'validation')
    {
        $this->lang = $lang;
        $this->group = $group;
        $this->namespace = $namespace;
        $this->basePath = $this->getTranslationsRootPath();
        $this->factory = new Factory($this->loadTranslator());
    }

    /**
     * @param string $path
     * @return $this
     */
    public function translationsRootPath(string $path = '')
    {
        if (!empty($path)) {
            $this->basePath = $path;
            $this->reloadValidatorFactory();
        }
        return $this;
    }

    /**
     * @return $this
     */
    private function reloadValidatorFactory()
    {
        $this->factory = new Factory($this->loadTranslator());
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslationsRootPath(): string
    {
        return dirname(__FILE__).'/../';
    }

    /**
     * @return Translator
     */
    public function loadTranslator(): Translator
    {
        $loader = new FileLoader(new Filesystem(), $this->basePath.$this->namespace);
        $loader->addNamespace($this->namespace, $this->basePath.$this->namespace);
        $loader->load($this->lang, $this->group, $this->namespace);
        return static::$translator = new Translator($loader, $this->lang);
    }

    /**
     * @param $method
     * @param $args
     * @return false|mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->factory, $method], $args);
    }
}
