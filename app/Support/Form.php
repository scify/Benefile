<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * The form helpers that the views use, as in laravelcollective/html.
 *
 * The views were written for laravelcollective/html, which is abandoned and
 * does not install on current Laravel versions. This class gives the same
 * calls (Form::text(), Form::radio() …) with the same behaviour, for the
 * methods the views use. Every value and attribute is escaped.
 *
 * The value of a field comes from, in this order: the old input after a
 * failed validation, the value given to the method, the model of Form::model().
 */
class Form
{
    private static mixed $model = null;

    /** @var array<string, true> The names that have a label: their fields get the name as id. */
    private static array $labels = [];

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function open(array $options = []): HtmlString
    {
        $method = strtoupper((string) ($options['method'] ?? 'POST'));
        $attributes = [
            'method' => $method === 'GET' ? 'GET' : 'POST',
            'action' => url((string) ($options['url'] ?? request()->path())),
            'accept-charset' => 'UTF-8',
        ];
        if (! empty($options['files'])) {
            $attributes['enctype'] = 'multipart/form-data';
        }
        unset($options['method'], $options['url'], $options['files']);

        $html = '<form'.self::attributes($attributes + $options).'>';
        if ($method !== 'GET') {
            $html .= '<input type="hidden" name="_token" value="'.e(csrf_token()).'">';
        }
        if (! in_array($method, ['GET', 'POST'], true)) {
            $html .= '<input type="hidden" name="_method" value="'.e($method).'">';
        }

        return new HtmlString($html);
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function model(mixed $model, array $options = []): HtmlString
    {
        self::$model = $model;

        return self::open($options);
    }

    public static function close(): HtmlString
    {
        self::$model = null;

        return new HtmlString('</form>');
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function label(string $name, ?string $value = null, array $options = [], bool $escape = true): HtmlString
    {
        self::$labels[$name] = true;
        $text = $value ?? ucwords(str_replace('_', ' ', $name));

        return new HtmlString('<label'.self::attributes(['for' => $name] + $options).'>'.($escape ? e($text) : $text).'</label>');
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function text(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return self::input('text', $name, $value, $options);
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function hidden(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return self::input('hidden', $name, $value, $options);
    }

    /**
     * Accepts file($name, $options) and file($name, null, $options).
     *
     * @param  array<int|string, mixed>|null  $options
     * @param  array<int|string, mixed>  $more
     */
    public static function file(string $name, ?array $options = [], array $more = []): HtmlString
    {
        return new HtmlString('<input'.self::attributes(['type' => 'file', 'name' => $name] + ($options ?? []) + $more).'>');
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function textarea(string $name, mixed $value = null, array $options = []): HtmlString
    {
        $options += ['name' => $name, 'cols' => 50, 'rows' => 10];
        if (! isset($options['id']) && self::idFor($name) !== null) {
            $options['id'] = self::idFor($name);
        }

        return new HtmlString('<textarea'.self::attributes($options).'>'.e(self::value($name, $value)).'</textarea>');
    }

    /**
     * @param  array<int|string, mixed>  $list  Value => text.
     * @param  array<int|string, mixed>  $options
     */
    public static function select(string $name, array $list = [], mixed $selected = null, array $options = []): HtmlString
    {
        $selected = self::value($name, $selected);
        $options = ['name' => $name] + $options;
        if (! isset($options['id']) && self::idFor($name) !== null) {
            $options['id'] = self::idFor($name);
        }

        $html = '<select'.self::attributes($options).'>';
        foreach ($list as $value => $text) {
            $isSelected = is_array($selected)
                ? in_array((string) $value, array_map('strval', $selected), true)
                : $selected !== null && (string) $value === (string) $selected;
            $html .= '<option value="'.e((string) $value).'"'.($isSelected ? ' selected="selected"' : '').'>'.e((string) $text).'</option>';
        }

        return new HtmlString($html.'</select>');
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function checkbox(string $name, mixed $value = 1, ?bool $checked = null, array $options = []): HtmlString
    {
        return self::checkable('checkbox', $name, $value, $checked, $options);
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function radio(string $name, mixed $value = null, ?bool $checked = null, array $options = []): HtmlString
    {
        return self::checkable('radio', $name, $value ?? $name, $checked, $options);
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function submit(?string $value = null, array $options = []): HtmlString
    {
        return new HtmlString('<input'.self::attributes(['type' => 'submit', 'value' => $value] + $options).'>');
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    public static function button(?string $value = null, array $options = []): HtmlString
    {
        return new HtmlString('<button'.self::attributes(['type' => 'button'] + $options).'>'.e((string) $value).'</button>');
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    private static function input(string $type, string $name, mixed $value, array $options): HtmlString
    {
        $attributes = ['type' => $type, 'name' => $name, 'value' => self::value($name, $value)];
        if (! isset($options['id']) && self::idFor($name) !== null) {
            $attributes['id'] = self::idFor($name);
        }

        return new HtmlString('<input'.self::attributes($attributes + $options).'>');
    }

    /**
     * @param  array<int|string, mixed>  $options
     */
    private static function checkable(string $type, string $name, mixed $value, ?bool $checked, array $options): HtmlString
    {
        $key = self::dotted($name);
        $request = request();
        if ($request->hasSession() && $request->session()->hasOldInput()) {
            $old = $request->old($key);
            $checked = is_array($old) ? in_array((string) $value, array_map('strval', $old), true) : (string) $old === (string) $value;
        } elseif ($checked === null && self::$model !== null) {
            $current = data_get(self::$model, $key);
            $checked = $current !== null && (string) $current === (string) $value;
        }

        $attributes = ['type' => $type, 'name' => $name, 'value' => $value];
        if ($checked) {
            $attributes['checked'] = 'checked';
        }

        return new HtmlString('<input'.self::attributes($attributes + $options).'>');
    }

    private static function value(string $name, mixed $value): mixed
    {
        $key = self::dotted($name);
        $request = request();
        if ($request->hasSession() && $request->session()->hasOldInput($key)) {
            return $request->old($key);
        }
        if ($value !== null) {
            return $value;
        }

        return self::$model !== null ? data_get(self::$model, $key) : null;
    }

    /** "name[0][x]" becomes "name.0.x", the key of the old input. */
    private static function dotted(string $name): string
    {
        return trim(str_replace(['[]', '[', ']'], ['', '.', ''], $name), '.');
    }

    /** As laravelcollective/html: a field has the id of its label, when a label for it exists. */
    private static function idFor(string $name): ?string
    {
        return isset(self::$labels[$name]) ? $name : null;
    }

    /**
     * @param  array<int|string, mixed>  $attributes
     */
    private static function attributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $key => $value) {
            if (is_int($key)) {
                $key = $value;
            }
            if ($value === null || $value === false) {
                continue;
            }
            $html .= ' '.e((string) $key).'="'.e(is_bool($value) ? (string) $key : (string) $value).'"';
        }

        return $html;
    }
}
