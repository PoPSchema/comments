<?php

declare(strict_types=1);

namespace PoP\Comments\FieldInterfaceResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\Comments\TypeDataLoaders\CommentTypeDataLoader;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use PoP\ComponentModel\FieldInterfaceResolvers\AbstractQueryableSchemaFieldInterfaceResolver;

class CommentableFieldInterfaceResolver extends AbstractQueryableSchemaFieldInterfaceResolver
{
    public const NAME = 'Commentable';
    public function getInterfaceName(): string
    {
        return self::NAME;
    }

    public function getSchemaInterfaceDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('The entity can receive comments', 'comments');
    }

    public static function getFieldNamesToImplement(): array
    {
        return [
            'areCommentsOpen',
            'commentCount',
            'hasComments',
            'comments',
        ];
    }

    public function getSchemaFieldType(string $fieldName): ?string
    {
        $types = [
            'areCommentsOpen' => SchemaDefinition::TYPE_BOOL,
            'commentCount' => SchemaDefinition::TYPE_INT,
            'hasComments' => SchemaDefinition::TYPE_BOOL,
            'comments' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($fieldName);
    }

    public function isSchemaFieldResponseNonNullable(string $fieldName): bool
    {
        switch ($fieldName) {
            case 'areCommentsOpen':
            case 'commentCount':
            case 'hasComments':
            case 'comments':
                return true;
        }
        return parent::isSchemaFieldResponseNonNullable($fieldName);
    }

    public function getSchemaFieldDescription(string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'areCommentsOpen' => $translationAPI->__('Are comments open to be added to the custom post', 'pop-comments'),
            'commentCount' => $translationAPI->__('Number of comments added to the custom post', 'pop-comments'),
            'hasComments' => $translationAPI->__('Does the custom post have comments?', 'pop-comments'),
            'comments' => $translationAPI->__('Comments added to the custom post', 'pop-comments'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($fieldName);
    }

    public function getSchemaFieldArgs(string $fieldName): array
    {
        $schemaFieldArgs = parent::getSchemaFieldArgs($fieldName);
        switch ($fieldName) {
            case 'comments':
                // Retrieve the module to filter for comments from the DataLoader
                $instanceManager = InstanceManagerFacade::getInstance();
                $commentTypeDataLoader = $instanceManager->getInstance(CommentTypeDataLoader::class);
                if ($filterDataloadingModule = $commentTypeDataLoader->getFilterDataloadingModule()) {
                    // Retrieve all the schema definitions for the filter inputs
                    return array_merge(
                        $schemaFieldArgs,
                        $this->getFilterSchemaDefinitionItems($filterDataloadingModule)
                    );
                }
                break;
        }
        return $schemaFieldArgs;
    }
}