<?php
use Joomla\CMS\Uri\Uri;

$el = $this->el('pre');

$json = [];
$json['@context'] = 'https://schema.org';
$json['@type'] = $props['type'];

if (count((array) $children))
{
    foreach ($children as $child)
    {
	    $json = array_merge($json, getValues($child));
    }
}
?>

<?php if ($props['debug']) : ?>
<?php $link = 'https://search.google.com/test/rich-results?url=' . urlencode(Uri::current()); ?>
<?= $el($props, $attrs) ?>
<?= '&lt;script type="application/ld+json"&gt;'; ?>
<?= json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
<?= '&lt;/script&gt;' ?>
</pre>
<?= '<a href="' . $link . '" class="uk-button uk-button-primary" target="_blank">Rich Results Test</a>'; ?>
<?php endif ?>
