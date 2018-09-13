<?php

namespace GlobalCipta\Common\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Has Metadata trait.
 *
 * @author          veelasky <veelasky@gmail.com>
 */
trait MetadataAble
{
    /**
     * Determine if this model has metadata relationship.
     *
     * @return bool
     */
    public function hasMetadata()
    {
        if ($this instanceof Model) {
            return method_exists($this, $this->getMetadataRelationshipName());
        }

        return isset($this->metadata);
    }

    /**
     *
     */
    public function savingMetadataAttributes()
    {
        if (property_exists($this, 'metadataAttributes') && count($this->metadataAttributes) > 0 )
        {
            $properties = request()->only($this->metadataAttributes);

        } else if (property_exists($this, 'metadataIgnore') && count($this->metadataIgnore) > 0) {
            $default_excluded = ['_token'];
            array_push($default_excluded, ...$this->metadataIgnore);

            $properties = request()->except(array_merge(array_keys($this->attributes), $default_excluded));
        }


        if ($this->hasMetadata() AND count($properties) > 0) {
            foreach ($properties as $key => $property) {
                $type = 'string';

                if (isset($this->casts[$key])) {
                    $type = $this->casts[$key];
                }

                $this->{$this->getMetadataRelationshipName()}()->updateOrCreate([
                    'key' => $key,
                ], [
                    'key' => $key,
                    'value' => $property,
                    'type' => $type,
                ]);
            }
        }
    }

    /**
     * Get metadata object.
     *
     * @param string $key
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getMetadataObject($key)
    {
        $found = null;

        foreach ($this->getRelationValue($this->getMetadataRelationshipName())->reverse() as $metadata) {
            if ($metadata instanceof Model and $metadata->{$metadata->getKeyColumn()} == $key) {
                $found = $metadata;
                break;
            }
        }

        return $found;
    }

    /**
     * Get metadata value.
     *
     * @param $key
     *
     * @return mixed|null
     */
    public function getMetadata($key)
    {
        $object = $this->getMetadataObject($key);

        if ($object instanceof Model) {
            if ($object->hasGetMutator($key)) {
                return $object->{'get'.studly_case($key).'Attribute'}();
            }

            return $object->{$object->getValueColumn()};
        }

        return;
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (
            empty($value)
            and $this->hasMetadata()
            and $this->getMetadataRelationshipName() != $key
            and ! array_key_exists($key, $this->attributes)
        ) {
            return $this->getMetadata($key);
        }

        return $value;
    }

    /**
     * Get metadata relationship name.
     *
     * @return string
     */
    public function getMetadataRelationshipName()
    {
        return (property_exists($this, 'metadataRelation')) ? $this->metadataRelation : 'metadata';
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        $metadata = $this->{$this->getMetadataRelationshipName()}()->get();

        foreach ($metadata as $metadatum) {
            $attributes[$metadatum->key] = $this->getMetadata($metadatum->key);
        }

        return $attributes;
    }

    /**
     * The "booting" method of the model.
     */
    protected static function bootMetadataAble()
    {
        static::saved(function (Model $model) {
            $model->savingMetadataAttributes();

            return true;
        });
    }
}
