<?php

namespace YOOtheme;

use YOOtheme\Http\Uri;

const REGEX_LOOM = '#(?:loom\.com/share/|loom\.com/embed/)([^"&?/ ]{32})#i';
/**
 * @param string $link
 * @param array  $params
 * @param bool   $defaults
 *
 * @return false|string
 */
function iframeLoom($link, $props, $params = [], $defaults = true)
{
    $link = strval($link);
    $query = parse_url($link, PHP_URL_QUERY);

    if ($query) {
        parse_str($query, $_params);
        $params = array_merge($_params, $params);
    }

    if (preg_match(REGEX_LOOM, $link, $matches)) {

        if (!empty($props['video_timestamps'])) {
            $params['t'] = $props['video_timestamps'];
        }

        if (!empty($props['video_muted'])) {
            $params['muted'] = 'true';
        }

        if (!empty($props['video_autoplay'])) {
            $params['autoplay'] = 'true';
        }

        if (!empty($props['video_share'])) {
            $params['hide_share'] = 'true';
        }

        if (!empty($props['video_topbar'])) {
            $params['hideEmbedTopBar'] = true;
        }

        if (!empty($props['video_title'])) {
            $params['hide_title'] = 'true';
        }

        if (!empty($props['video_owner'])) {
            $params['hide_owner'] = 'true';
        }

        if (!empty($props['video_speed'])) {
            $params['hide_speed'] = 'true';
        }



        return Url::to(
            "https://loom.com/embed/{$matches[1]}",
            $defaults
                ? array_merge(
                    [
                        'rel' => 0,
                        'loop' => 1,
                        'playlist' => $matches[2],
                        'autoplay' => 1,
                        'controls' => 0,
                        'showinfo' => 0,
                        'iv_load_policy' => 3,
                        'modestbranding' => 1,
                        'wmode' => 'transparent',
                        'playsinline' => 1,
                    ],
                    $params,
                )
                : $params,
        );
    }

    return false;
}

/** @var ImageProvider $imageProvider */
$imageProvider = app(ImageProvider::class);

$el = $this->el('div');

// Video
if ($iframe = iframeLoom($props['video'], $props, [],false)) {

    $video = $this->el('iframe', [

        'src' => $iframe,
        'allow' => 'autoplay',
        'webkitallowfullscreen' => true,
        'mozallowfullscreen' => true,
        'allowfullscreen' => true,
        'uk-responsive' => true,
        'loading' => ['lazy {@video_lazyload}'],

    ]);

    if ($props['video_width'] && !$props['video_height']) {
        $props['video_height'] = round($props['video_width'] * 9 / 16);
    } elseif ($props['video_height'] && !$props['video_width']) {
        $props['video_width'] = round($props['video_height'] * 16 / 9);
    }

} else {

    $video = $this->el('video', [

        'src' => $props['video'],
        'preload' => ['none {@video_lazyload}'],

    ]);

    if ($props['video_width'] && $props['video_height']) {
        $video->attr(['style' => ["aspect-ratio: {$props['video_width']} / {$props['video_height']};"]]);
    }

}

$video->attr([

    'class' => [
        'uk-box-shadow-{video_box_shadow}',
    ],

    'width' => $props['video_width'],
    'height' => $props['video_height'],

]);

// Box decoration
$decoration = $this->el('div', [

    'class' => [
        'uk-box-shadow-bottom {@video_box_decoration: shadow}',
        'tm-mask-default {@video_box_decoration: mask}',
        'tm-box-decoration-{video_box_decoration: default|primary|secondary}',
        'tm-box-decoration-inverse {@video_box_decoration_inverse} {@video_box_decoration: default|primary|secondary}',
        'uk-inline {@!video_box_decoration: |shadow}',
    ],

]);

?>

<?= $el($props, $attrs) ?>

    <?php if ($props['video_box_decoration']) : ?>
    <?= $decoration($props) ?>
    <?php endif ?>

        <?= $video($props, '') ?>

    <?php if ($props['video_box_decoration']) : ?>
    <?= $decoration->end() ?>
    <?php endif ?>

<?= $el->end() ?>
