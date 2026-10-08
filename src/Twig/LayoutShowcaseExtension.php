<?php

declare(strict_types=1);

namespace DigitaleDinge\ContaoKissQuickstartDeveloper\Twig;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\SelectMenu;
use Contao\StringUtil;
use DigitaleDinge\ContaoKiss\Styles\Option\Layout;
use DigitaleDinge\ContaoKiss\Styles\Option\Margin;
use DigitaleDinge\ContaoKiss\Styles\Option\Padding;
use DigitaleDinge\ContaoKiss\Styles\StyleOptionRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LayoutShowcaseExtension extends AbstractExtension
{
    /**
     * Field => style option, the same pairs content_element/_base.html.twig resolves.
     */
    private const array FIELDS = [
        'contentWidth' => Layout\ContainerOption::class,
        'marginTop' => Margin\TopOption::class,
        'paddingTop' => Padding\TopOption::class,
        'marginBottom' => Margin\BottomOption::class,
        'paddingBottom' => Padding\BottomOption::class,
    ];

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly StyleOptionRegistry $registry,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('kiss_layout_showcase', $this->getShowcase(...)),
        ];
    }

    /**
     * @param array<string, string> $values
     *
     * @return array{form: string, fields: array<string, array{label: string, value: string, classes: array<string, string>}>}
     */
    public function getShowcase(array $values = []): array
    {
        $this->framework->initialize();
        $this->framework->getAdapter(Controller::class)->loadDataContainer('tl_content');

        $form = '';
        $fields = [];

        foreach (self::FIELDS as $field => $option) {
            $dca = $GLOBALS['TL_DCA']['tl_content']['fields'][$field];
            $attributes = SelectMenu::getAttributesFromDca($dca, $field, $values[$field] ?? '', $field, 'tl_content');

            $form .= \sprintf(
                '<div class="%s widget">%s<p class="tl_help tl_tip">%s</p></div>',
                $dca['eval']['tl_class'] ?? '',
                (new SelectMenu($attributes))->parse(),
                StringUtil::specialchars($dca['label'][1] ?? ''),
            );

            $classes = [];

            foreach ($attributes['options'] ?? [] as $choice) {
                if ('' !== ($choice['value'] ?? '')) {
                    $classes[$choice['value']] = trim((string) $this->registry->create($option, $choice['value']));
                }
            }

            $fields[$field] = [
                'label' => $dca['label'][0] ?? $field,
                'value' => $values[$field] ?? '',
                'classes' => $classes,
            ];
        }

        return ['form' => $form, 'fields' => $fields];
    }
}
