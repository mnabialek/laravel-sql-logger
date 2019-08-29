<?php

namespace Mnabialek\LaravelSqlLogger\Objects\Concerns;

use DateTime;

trait ReplacesBindings
{
    /**
     * Replace bindings.
     *
     * @param string $sql
     * @param array $bindings
     *
     * @return string
     */
    protected function replaceBindings($sql, array $bindings)
    {
        $generalRegex = $this->getRegex();

        foreach ($this->formatBindings($bindings) as $key => $binding) {
            $regex = is_numeric($key) ? $generalRegex : $this->getNamedParameterRegex($key);
            $sql = preg_replace($regex, $this->value($binding), $sql, 1);
        }

        return $sql;
    }

    /**
     * Get final value that will be displayed in query.
     *
     * @param mixed $value
     *
     * @return int|string
     */
    protected function value($value)
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return (int) $value;
        }

        return is_numeric($value) ? $value : "'" . $value . "'";
    }

    /**
     * Get regex to be used for named parameter with given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getNamedParameterRegex($name)
    {
        if (mb_substr($name, 0, 1) == ':') {
            $name = mb_substr($name, 1);
        }

        return $this->wrapRegex($this->notInsideQuotes('\:' . preg_quote($name), false));
    }

    /**
     * Format bindings values.
     *
     * @param array $bindings
     *
     * @return array
     */
    protected function formatBindings($bindings)
    {
        foreach ($bindings as $key => $binding) {
            if ($binding instanceof DateTime) {
                $bindings[$key] = $binding->format('Y-m-d H:i:s');
            } elseif (is_string($binding)) {
                $bindings[$key] = str_replace("'", "\\'", $binding);
            }
        }

        return $bindings;
    }

    /**
     * Get regex to be used to replace bindings.
     *
     * @return string
     */
    protected function getRegex()
    {
        return $this->wrapRegex(
            $this->notInsideQuotes('?')
            . '|' .
            $this->notInsideQuotes('\:\w+', false)
        );
    }

    /**
     * Wrap regex.
     *
     * @param string $regex
     *
     * @return string
     */
    protected function wrapRegex($regex)
    {
        return '#' . $regex . '#ms';
    }

    /**
     * Create partial regex to find given text not inside quotes.
     *
     * @param string $string
     * @param bool $quote
     *
     * @return string
     */
    protected function notInsideQuotes($string, $quote = true)
    {
        if ($quote) {
            $string = preg_quote($string);
        }

        return
            // double quotes - ignore "" and everything inside quotes for example " abc \"err "
            '(?:""|"(?:[^"]|\\")*?[^\\\]")(*SKIP)(*F)|' . $string .
            '|' .
            // single quotes - ignore '' and everything inside quotes for example ' abc \'err '
            '(?:\\\'\\\'|\'(?:[^\']|\\\')*?[^\\\]\')(*SKIP)(*F)|' . $string;
    }
}
