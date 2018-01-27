<?php
/**
 * The Thing Schema.
 *
 * In the spec, the "Thing" is the top-most, abstract parent for all other schemas.
 *
 * @package Schemify
 * @link    http://schema.org/Thing
 */

namespace Schemify\Schemas;

use Schemify\Core as Core;

class Thing implements \JsonSerializable {

	/**
	 * The data to display with this Schema.
	 *
	 * @var array $data
	 */
	protected $data;

	/**
	 * Is this instance the top-level schema for the current post object?
	 *
	 * @var bool $isMain
	 */
	protected $isMain;

	/**
	 * The ID of the post or object this schema represents.
	 *
	 * @var int $postId;
	 */
	protected $postId;

	/**
	 * A registry of all properties available for this schema.
	 *
	 * @var array $propertyList
	 */
	protected $propertyList;

	/**
	 * The Schema this class represents.
	 *
	 * @var string $schema
	 */
	protected $schema;

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'additionalType',
		'alternateName',
		'description',
		'disambiguatingDescription',
		'identifier',
		'image',
		'mainEntityOfPage',
		'name',
		'potentialAction',
		'sameAs',
		'url',
	);

	/**
	 * Properties that would have normally been inherited but should not exist in a sub-tree.
	 *
	 * For example, the "image" property is useful until you get into the ImageObject schema (or
	 * anything that extends that), since you're describing an image with another image.
	 *
	 * @var array $removeProperties
	 */
	protected static $removeProperties = array();

	/**
	 * Class constructor, which automatically maps passed values to $this->data.
	 *
	 * @param int  $post_id The ID of the post being represented by this object.
	 * @param bool $is_main Optional. Is this the top-level Schema for this object? Default is false.
	 */
	public function __construct( $post_id, $is_main = false ) {
		$this->postId = $post_id;
		$this->isMain = (bool) $is_main;
	}

	/**
	 * Retrieve the data that has been stored for this object.
	 *
	 * @return array The contents of $this->data.
	 */
	public function getProperties() {
		if ( ! $this->data ) {
			$data   = $this->build( $this->postId, $this->isMain );
			$schema = $this->getSchema();
			$filter = sprintf( 'schemify_get_properties_%s', $schema );

			/**
			 * Filter the output for the given schema.
			 *
			 * @param array  $data      The collection of properties assembled for this object.
			 * @param string $schema    The current schema being filtered.
			 * @param int    $object_id The object ID being constructed.
			 * @param bool   $is_main   Is this the top-level JSON-LD schema being constructed?
			 */
			$data = apply_filters( $filter, $data, $schema, $this->postId, $this->isMain );

			/**
			 * Filter the output for all schemas.
			 *
			 * @param array  $data      The collection of properties assembled for this object.
			 * @param string $schema    The current schema being filtered.
			 * @param int    $object_id The object ID being constructed.
			 * @param bool   $is_main   Is this the top-level JSON-LD schema being constructed?
			 */
			$this->data = apply_filters( 'schemify_get_properties', $data, $schema, $this->postId, $this->isMain );
		}

		return array_filter( (array) $this->data );
	}

	/**
	 * Utility method to retrieve the property with key $property via the associated `get*` method.
	 *
	 * @param string $property The Schema.org property to retrieve a value for.
	 * @return mixed Either the return value of the getter method or NULL.
	 */
	public function getProp( $property ) {
		$method = sprintf( 'get%s', ucwords( $property ) );

		return method_exists( $this, $method ) ? $this->$method( $this->postId ) : null;
	}

	/**
	 * Control what's visible when the object is converted into JSON.
	 *
	 * @return array The object properties that should be serialized.
	 */
	public function jsonSerialize() {
		return $this->getProperties();
	}

	/**
	 * Get the schema name this class represents.
	 *
	 * Since PHP doesn't offer an easy way to get the non-namespaced class name, we'll cache the
	 * result in a local property.
	 *
	 * @return string The current class name, devoid of any namespacing.
	 */
	public function getSchema() {
		if ( ! $this->schema ) {
			$this->schema = Core\strip_namespace( get_class( $this ) );
		}

		return $this->schema;
	}

	/**
	 * Populate the $data attribute for the given post.
	 *
	 * @param int  $post_id The ID of the post being represented by this object.
	 * @param bool $is_main Whether or not this is the top-level schema being built.
	 */
	protected function build( $post_id, $is_main ) {

		// Placeholder is using %s as this *can* be a non-integer value (e.g. "home").
		$cache_key = sprintf( 'schema_%s', $post_id );

		// Return early if we have a cached version.
		$cached = wp_cache_get( $cache_key, 'schemify', false );
		if ( $is_main && $cached ) {
			return $cached;
		}

		// Build the data array.
		$data = array();

		foreach ( $this->getPropertyList() as $prop ) {
			$data[ $prop ] = $this->getProp( $prop );
		}

		// Merge in defaults and protected properties.
		$data = array_merge( array(
			'@context' => $is_main ? 'http://schema.org' : null,
			'@type'    => $this->getSchema(),
		), $data );

		// Cache the result (top-level only) so we don't have to calculate it every time.
		if ( $is_main ) {
			wp_cache_set( $cache_key, $data, 'schemify', 0 );
		}

		// Finally, return the value.
		return $data;
	}

	/**
	 * Iterate through all parent schemas to build a list of available properties for this schema.
	 */
	protected function getPropertyList() {
		if ( $this->propertyList ) {
			return $this->propertyList;
		}

		// Iterate through the list of parent classes to get their $properties properties.
		$class   = get_class( $this );
		$parents = array_reverse( class_parents( $this ) );

		// Be sure to include the current class at the end of the list.
		$parents[ $class ] = $class;

		// Now that we have an array of property additions/deletions keyed by their schema, merge 'em.
		$properties = array_reduce( $parents, function ( $list, $schema ) {
			$props = array_merge( $list, $schema::$properties );
			return array_diff( $props, $schema::$removeProperties );
		}, array() );

		// Ensure we don't have duplicates.
		$properties = array_unique( $properties );
		sort( $properties );

		// Save the value in $this->propertyList.
		$this->propertyList = $properties;

		return $this->propertyList;
	}

	/**
	 * Everything below this comment should be a getter method, called dynamically via the getProp()
	 * method in this class.
	 *
	 * Each method accepts exactly one argument: a post ID.
	 */

	/**
	 * Retrieve the description for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post's excerpt/description.
	 */
	public function getDescription( $post_id ) {
		return esc_html( get_the_excerpt( $post_id ) );
	}

	/**
	 * Retrieve the name of a post.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post's title.
	 */
	public function getName( $post_id ) {
		return get_the_title( $post_id );
	}

	/**
	 * Retrieve the image for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return ImageObject An image object representing the post.
	 */
	public function getImage( $post_id ) {
		if ( ! $this->isMain ) {
			return null;
		}

		$post_thumbnail = get_post_thumbnail_id( $post_id );

		return $post_thumbnail ? new ImageObject( $post_thumbnail ) : null;
	}

	/**
	 * Retrieve the URL for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return string The post's URL.
	 */
	public function getUrl( $post_id ) {
		return get_permalink( $post_id );
	}
}
