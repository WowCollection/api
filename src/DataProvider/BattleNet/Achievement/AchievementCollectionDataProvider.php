<?php

namespace App\DataProvider\BattleNet\Achievement;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\DataProvider\BattleNet\AbstractBattleNetDataProvider;
use App\DataProvider\Traits\RealmFilterTrait;
use App\DataTransformer\AchievementTransformer;
use App\Entity\Achievement;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AchievementCollectionDataProvider
 * @property AchievementTransformer $transformer
 */
class AchievementCollectionDataProvider extends AbstractBattleNetDataProvider implements CollectionDataProviderInterface
{
    use RealmFilterTrait;

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Achievement::class === $resourceClass;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @return array|mixed
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        if ($operationName === 'character_achievements') {
            $realm = $this->getRealm();

            $character = $this->getRequest()->attributes->get('character');
            $character = $this->battleNetSDK->getCharacter($character, $realm, 'achievements');

            $achievements = array_combine(
                $character['achievements']['achievementsCompletedTimestamp'],
                $character['achievements']['achievementsCompleted']
            );

            array_walk($achievements, function (&$achievement, $timestamp) {
                $achievement = $this->battleNetSDK->getAchievement($achievement);
                $achievement = $this->transformer->transformItem($achievement);
                $achievement->setCompletedAt($this->battleNetSDK->formatTimestamp($timestamp));
            });

            $collection = new ArrayCollection($achievements);

            return $this->paginate($collection, $resourceClass, $operationName);
        }

        throw new ResourceClassNotSupportedException();
    }
}
