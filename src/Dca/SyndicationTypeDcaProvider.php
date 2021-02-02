<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Dca;

use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;
use Symfony\Contracts\Translation\TranslatorInterface;

class SyndicationTypeDcaProvider extends AbstractDcaProvider
{
    /**
     * @var SyndicationTypeCollection
     */
    protected $typeCollection;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(SyndicationTypeCollection $typeCollection, TranslatorInterface $translator)
    {
        $this->typeCollection = $typeCollection;
        $this->translator = $translator;
    }

    /**
     * @return string[]
     */
    public function getSubpalettes(): array
    {
        return [
            'syndicationEmail' => 'syndicationEmailSubject,syndicationEmailBody',
            'syndicationFeedbackEmail' => 'syndicationEmailAddress,syndicationEmailSubject,syndicationEmailBody',
        ];
    }

    /**
     * @return string[]
     */
    public function getPalettesSelectors(): array
    {
        return [
            'syndicationEmail',
            'syndicationFeedbackEmail',
        ];
    }

    /**
     * @return array[]
     */
    public function getFields(): array
    {
        $fields = [];

        $fields['syndicationFacebook'] = [
            'label' => $this->getLabel('syndicationFacebook'),
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default ''",
        ];

        $fields['syndicationEmail'] = [
            'label' => $this->getLabel('syndicationEmail'),
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql' => "char(1) NOT NULL default ''",
        ];
        $fields['syndicationFeedbackEmail'] = [
            'label' => $this->getLabel('syndicationFeedbackEmail'),
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql' => "char(1) NOT NULL default ''",
        ];
        $fields['syndicationEmailAddress'] = [
            'label' => $this->getLabel('syndicationEmailAddress'),
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ];
        $fields['syndicationEmailSubject'] = [
            'label' => $this->getLabel('syndicationEmailSubject'),
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ];

        $fields['syndicationEmailBody'] = [
            'label' => $this->getLabel('syndicationEmailBody'),
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['maxlength' => 1000, 'tl_class' => 'long clr', 'rows' => 3],
            'sql' => 'text NULL',
        ];

        return $fields;
    }

    /**
     * Return the dca palette for syndication types.
     *
     * @param bool $splitByCategories Set to false, if you want all fields are within an single syndication type legend.
     *                                Otherwise multiple legends returned based on categories of syndication (typical export and share).
     */
    public function getPalette(bool $splitByCategories = true): string
    {
        $palette = '';

        $categories = $this->typeCollection->getCategories();

        foreach ($categories as $category) {
            $fields = [];
            $types = $this->typeCollection->getTypesByCategory($category);

            foreach ($types as $type) {
                $fields[] = $type::getActivationField();
            }

            if (empty($fields)) {
                continue;
            }

            if ($splitByCategories) {
                $palette .= '{'.$category.'_legend},';
            }
            $palette .= implode(',', $fields).';';
        }

        if (!$splitByCategories) {
            $palette = '{syndication_type_legend},'.$palette;
        }

        return $palette;
    }
}
