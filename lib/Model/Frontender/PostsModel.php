<?php

declare(strict_types=1);

namespace Frontender\Platform\Model\Frontender;

class PostsModel extends \Frontender\Platform\Model\Wordpress\PostsModel {
	/**
	 * This method will return all featured_media from Advanced Custom Fields.
	 *
	 * @return array The media items from ACF.
	 */
	public function getPropertyFeaturedMedia(): array {
		if(!isset($this['acf']['featured_media']) && !is_array($this['acf']['featured_media'])) {
			return [];
		}

		$mediaIDs = array_column($this['acf']['featured_media'], 'ID');
		$media = array_map(function($mediaID) {
			$model = new MediaModel($this->container);
			$model->setState([
				'id' => $mediaID
			]);

			try {
				$media = $model->fetch();
				$medium = array_shift($media);

				if(!$medium) {
					return false;
				}

				return $medium;
			} catch(\Error $e) {
				return false;
			} catch(\Exception $e) {
				return false;
			}
		}, $mediaIDs);

		return array_filter($media, function($medium) {
			return !!$medium;
		});
	}

	public function getPropertyFeaturedMediaAttribution() {
		return isset($this['acf']['featured_media_attribution']) && !empty($this['acf']['featured_media_attribution']) ? $this['acf']['featured_media_attribution'] : false;
	}
}