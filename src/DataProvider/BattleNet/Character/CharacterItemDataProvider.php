<?php

namespace App\DataProvider\BattleNet\Character;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\DataProvider\BattleNet\AbstractBattleNetDataProvider;
use App\DataProvider\Traits\RealmFilterTrait;
use App\DataTransformer\CharacterTransformer;
use App\DataTransformer\ItemsTransformer;
use App\Entity\Character;

/**
 * Class CharacterItemDataProvider
 * @property CharacterTransformer $transformer
 */
class CharacterItemDataProvider extends AbstractBattleNetDataProvider implements ItemDataProviderInterface
{
    use RealmFilterTrait;

    public $model = Character::class;

    /**
     * Retrieves an item.
     *
     * @param string $resourceClass
     * @param array|int|string $id
     * @param string|null $operationName
     * @param array $context
     * @return Character
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Character
    {
        if ($operationName === 'get') {
            $realm = $this->getRealm();

            return $this->transformer->transformItem($this->battleNetSDK->getCharacter($id, $realm));
        }

        if ($operationName === 'character_guild') {
            $realm = $this->getRealm();

            $itemsTransformer = $this->container->get(ItemsTransformer::class);
            return $itemsTransformer->transformItem($this->battleNetSDK->getCharacter($id, $realm, 'guild'));
        }


        throw new ResourceClassNotSupportedException();
    }

    public static function getSubscribedServices()
    {
        return [
          'App\DataTransformer\ItemsTransformer'
        ];
    }
}
