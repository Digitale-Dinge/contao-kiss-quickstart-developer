<?php

declare(strict_types=1);

namespace DigitaleDinge\ContaoKissQuickstartDeveloper\Twig;

use Composer\InstalledVersions;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BundleVersionsExtension extends AbstractExtension
{
    private const array BUNDLES = ['contao-kiss', 'contao-kiss-elements', 'contao-kiss-quickstart-developer', 'contao-company'];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('kiss_bundle_versions', $this->getBundleVersions(...)),
        ];
    }

    /**
     * @return array<string, array{reference: string|null, remote: string|null, behind: int|null}>
     */
    public function getBundleVersions(): array
    {
        $bundles = [];

        foreach (self::BUNDLES as $name) {
            if (!InstalledVersions::isInstalled('digitaledinge/'.$name)) {
                continue;
            }

            $reference = InstalledVersions::getReference('digitaledinge/'.$name);
            $remote = $this->cache->get('kiss_bundle_versions_'.$name.'_'.$reference, function (ItemInterface $item) use ($name, $reference): ?array {
                $item->expiresAfter(600);

                try {
                    $compare = $this->httpClient
                        ->request('GET', "https://api.github.com/repos/Digitale-Dinge/$name/compare/$reference...main", ['timeout' => 3])
                        ->toArray()
                    ;
                } catch (ExceptionInterface) {
                    return null;
                }

                // Without paging parameters, GitHub ends the commit list with the head of main
                return [
                    'sha' => $compare['ahead_by'] ? end($compare['commits'])['sha'] : $compare['merge_base_commit']['sha'],
                    'behind' => $compare['ahead_by'],
                ];
            });

            $bundles[$name] = [
                'reference' => $reference,
                'remote' => $remote['sha'] ?? null,
                'behind' => $remote['behind'] ?? null,
            ];
        }

        return $bundles;
    }
}
