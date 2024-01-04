<?php
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

$properties = [];

if ($props['og_url']) {

	$properties[] = [
		'property' => 'og:url',
		'content' => Uri::current(),
	];
}

if (!is_null($fbid = $props['facebookid']) && $fbid !== '')
{

	$properties[] = [
		'property' => 'fb:app:id',
		'content' => $fbid,
	];
}

if (count((array) $children))
{
	foreach ($children as $child)
	{
		$value = '';
		$key = $child->props['property'];
		$image = $child->props['image'];
		$content = $child->props['content'];
		$check_exists = $child->props['check_exists'];

		if ((is_null($key) && $key === '') || (is_null($image) && $image === '' && is_null($content) && $content === ''))
		{
			continue;
		}

		if (!is_null($image) && $image !== '')
		{
			$img = HTMLHelper::_('cleanImageURL', $image);
			$image = $img->url === null ? '' : htmlspecialchars($img->url, ENT_QUOTES, 'UTF-8');

			if (!is_null($image) && $image !== '' && File::exists($image))
			{
				$value = Uri::base() . $image;
			}
		}

		if (!is_null($content) && $content !== '')
		{
			$value = $content;
		}

		$properties[] = [
			'property' => $key,
			'content' => $value,
		];
	}
}

?>

<?php if ($props['debug']) :?>
    <pre>
<?php foreach ($properties as $property): ?>
	<?= '&lt;meta ' . ArrayHelper::toString($property) . '&gt;'; ?><br>
<?php endforeach; ?>
    </pre>
	<?php $link = 'https://developers.facebook.com/tools/debug/?q=' . urlencode(Uri::current()); ?>
	<?= '<a href="' . $link . '" class="uk-button uk-button-primary" target="_blank">Open Facebook Object Debugger</a>'; ?>
<?php endif ?>
