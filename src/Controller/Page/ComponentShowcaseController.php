<?php

declare(strict_types=1);

namespace DigitaleDinge\ContaoKissQuickstartDeveloper\Controller\Page;

use Contao\CoreBundle\ContentComposition\ContentComposition;
use Contao\CoreBundle\DependencyInjection\Attribute\AsPage;
use Contao\CoreBundle\Twig\Loader\ContaoFilesystemLoader;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsPage]
readonly class ComponentShowcaseController
{
    private const string TEMPLATE_NAMESPACE = 'kiss/showcase/components/';

    public function __construct(
        private ContentComposition $contentComposition,
        private Environment $twig,
        private ContaoFilesystemLoader $filesystemLoader,
    ) {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function __invoke(PageModel $pageModel): Response
    {
        $layoutTemplate = $this->contentComposition
            ->createContentCompositionBuilder($pageModel)
            ->buildLayoutTemplate()
        ;

        $layoutTemplate->setSlot('main', $this->twig->render(
            '@Contao/kiss/showcase/components.html.twig', [
                'components' => $this->getShowcaseTemplates(),
            ]
        ));

        return $layoutTemplate->getResponse();
    }

    private function getShowcaseTemplates(): array
    {
        $templates = [];

        $chains = $this->filesystemLoader->getInheritanceChains();

        $matching = array_filter(
            $chains,
            fn(string $key): bool => str_starts_with($key, self::TEMPLATE_NAMESPACE),
            ARRAY_FILTER_USE_KEY
        );

        foreach ($matching as $key => $value) {
            $templates[substr($key, strlen(self::TEMPLATE_NAMESPACE))] = reset($value);
        }

        return $templates;
    }
}
