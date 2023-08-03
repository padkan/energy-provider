<?php
namespace Core;

/**
 * [Class base view]
 */
class BaseView {

    /**
     * render view
     * @param string $view
     * @param array $args
     * 
     * @return [type]
     */
    public static function render(string $view, array $args = []) {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view"; 
        if (is_readable($file)) {
            require $file;
        } else {
            //echo "$file not found";
            throw new \Exception("$file not found");
        }
    }

    /**
     * render template by twig
     * @param string $template
     * @param array $args
     * 
     * @return void
     */
    public static function renderTemplate(string $template, array $args = []) :void {
        echo static::getTemplate($template, $args);
    }

        /**
     * render template by twig
     * @param string $template
     * @param array $args
     * 
     * @return string
     */
    public static function getTemplate(string $template, array $args = []) :string {
        static $twig = null;
        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig\Environment($loader);
            //$twig->addGlobal('session', $_SESSION);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
        }
        return $twig->render($template, $args);
    }

}