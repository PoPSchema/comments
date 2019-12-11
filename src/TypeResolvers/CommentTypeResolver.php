<?php
namespace PoP\Comments\TypeResolvers;

use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;
use PoP\Comments\TypeDataLoaders\CommentTypeDataLoader;

class CommentTypeResolver extends AbstractTypeResolver
{
    public const NAME = 'Comment';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getId($resultItem)
    {
        $cmscommentsresolver = \PoP\Comments\ObjectPropertyResolverFactory::getInstance();
        $comment = $resultItem;
        return $cmscommentsresolver->getCommentId($comment);
    }

    public function getTypeDataLoaderClass(): string
    {
        return CommentTypeDataLoader::class;
    }
}

