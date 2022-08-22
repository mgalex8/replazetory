<?php
namespace App\Bundle\Templates\Themes;

/**
 * Class AnyParameterTemplateTheme
 */
class AnyParameterTemplateTheme extends Theme implements IThemeInterface
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var
     */
    protected $name;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Class Constructor.
     * @param string $name
     * @param string $path
     * @param array $additional_parameters
     * @param string $description
     * @param string $preview_image
     * @param string $author
     * @param string $reviews
     * @param int $view_count
     * @param int $download_count
     * @param string $download_url
     */
    public function __construct(string $name, string $path, array $additional_parameters = [], string $title = '', string $description = '', string $categories = '', string $tags = '', string $keywords = '', string $preview_image = '', string $author = '', string $reviews = '', int $view_count = 0, int $download_count = 0, string $download_url = '', string $colors = 'red|blue')
    {
        $this->name = $name;
        $this->path = $path;
        $this->parameters = array_merge($additional_parameters, [
            'title'             => isset($additional_parameters['title']) && empty($title)                   ? $additional_parameters['title']          : $title,
            'description'       => isset($additional_parameters['description']) && empty($description)       ? $additional_parameters['description']    : $description,
            'categories'        => isset($additional_parameters['categories']) && empty($categories)         ? $additional_parameters['categories']     : $categories,
            'tags'              => isset($additional_parameters['tags']) && empty($tags)                     ? $additional_parameters['tags']           : $tags,
            'keywords'          => isset($additional_parameters['keywords']) && empty($keywords)             ? $additional_parameters['keywords']       : $keywords,
            'preview_image'     => isset($additional_parameters['preview_image']) && empty($preview_image)   ? $additional_parameters['preview_image']  : $preview_image,
            'author'            => isset($additional_parameters['author']) && empty($author)                 ? $additional_parameters['author']         : $author,
            'reviews'           => isset($additional_parameters['reviews']) && empty($reviews)               ? $additional_parameters['reviews']        : $reviews,
            'view_count'        => isset($additional_parameters['view_count']) && empty($view_count)         ? $additional_parameters['view_count']     : $view_count,
            'download_count'    => isset($additional_parameters['download_count']) && empty($download_count) ? $additional_parameters['download_count'] : $download_count,
            'download_url'      => isset($additional_parameters['download_url']) && empty($download_url)     ? $additional_parameters['download_url']   : $download_url,
            'colors'            => isset($additional_parameters['colors']) && empty($colors)                 ? $additional_parameters['colors']         : $colors,
        ]);
    }

    /**
     * Set Category for this Theme
     * @param string|array $categories String parameter delimiter by $delimiter or array parameter
     * @param string $delimiter Delimiter for explode string $key$categorieswords parameter
     * @return void
     * @throws \Exception
     */
    public function setCategories($categories, string $delimiter = '|')
    {
        if (is_array($categories) || $categories instanceof \Iterator) {
            foreach ($categories as $category) {
                if (! is_string($category)) {
                    throw new \LogicException(sprintf('Parameter `$keywords` for %s must be string, but %s given', __METHOD__, is_object($category) ? get_class($category) : gettype($category)));
                }
            }
            $this->parameters['categories'] = $categories;
        } elseif (is_string($categories)) {
            $this->parameters['categories'] = explode('|', $categories);
        } else {
            throw new \Exception(sprintf('Parameter $categories for %s must be array or string with delimiter `%s`, but &s given', __METHOD__, $delimiter, is_object($categories) ? get_class($categories) : gettype($categories)));
        }
    }

    /**
     * Set Tags for this Theme with delimiter
     * @param string|array $tags String parameter delimiter by $delimiter or array parameter
     * @param string $delimiter Delimiter for explode string $tags parameter
     * @return void
     * @throws \Exception
     */
    public function setTags($tags, string $delimiter = '|')
    {
        if (is_array($tags) || $tags instanceof \Iterator) {
            foreach ($tags as $tag) {
                if (! is_string($tag)) {
                    throw new \LogicException(sprintf('Parameter `$tags` for %s must be string, but %s given', __METHOD__, is_object($tag) ? get_class($tag) : gettype($tag)));
                }
            }
            $this->parameters['tags'] = $tags;
        } elseif (is_string($tags)) {
            $this->parameters['tags'] = explode($delimiter, $tags);
        } else {
            throw new \Exception(sprintf('Parameter $tags for %s must be array or string with delimiter `%s`, but &s given', __METHOD__, $delimiter, is_object($tags) ? get_class($tags) : gettype($tags)));
        }
    }

    /**
     * Set Keywords for this Theme
     * @param string|array $keywords String parameter delimiter by $delimiter or array parameter
     * @param string $delimiter Delimiter for explode string $keywords parameter
     * @return void
     * @throws \Exception
     */
    public function setKeywords($keywords, string $delimiter = '|')
    {
        if (is_array($keywords) || $keywords instanceof \Iterator) {
            foreach ($keywords as $keyword) {
                if (! is_string($keyword)) {
                    throw new \LogicException(sprintf('Parameter `$keywords` must be string, but %s given', is_object($keyword) ? get_class($keyword) : gettype($keyword)));
                }
            }
            $this->parameters['keywords'] = $keywords;
        } elseif (is_string($keywords)) {
            $pattern = '/\\'.$delimiter.'+(\s)+'
                .'|'.
                '(\s)+\\'.$delimiter.'+(\s)+'
                .'|'.
                '(\s)+\\'.$delimiter.'+'
                .'|'.
                '\\'.$delimiter.'+/';

            // set keywords delimiter by $delimiter
            $this->parameters['keywords'] = preg_split($pattern, $keywords);

            // set keywords delimiter by word
            $prepared = trim( preg_replace('/[^0-9\p{Cyrillic}\p{Latin}]/i', $delimiter, $keywords), $delimiter);
            $words = preg_split('/[\s\\'.$delimiter.']+/', $prepared);
            foreach ($words as $word) {
                if (! in_array($word, $this->parameters['keywords'])) {
                    $this->parameters['keywords'][] = $word;
                }
            }
        } else {
            throw new \Exception(sprintf('Parameter $keywords for %s must be array or string with delimiter `%s`, but &s given', __METHOD__, $delimiter, is_object($keywords) ? get_class($keywords) : gettype($keywords)));
        }
    }

}