<?php
/**
 * @group post
 * @group navmenus
 */
class Test_Nav_Menus extends WP_UnitTestCase {
	/**
	 * @var int
	 */
	public $menu_id;

	function setUp() {
		parent::setUp();

		$this->menu_id = wp_create_nav_menu( rand_str() );
	}

	function test_wp_get_associated_nav_menu_items() {
		$tag_id = $this->factory->tag->create();
		$cat_id = $this->factory->category->create();
		$post_id = $this->factory->post->create();
		$post_2_id = $this->factory->post->create();
		$page_id = $this->factory->post->create( array( 'post_type' => 'page' ) );

		$tag_insert = wp_update_nav_menu_item( $this->menu_id, 0, array(
			'menu-item-type' => 'taxonomy',
			'menu-item-object' => 'post_tag',
			'menu-item-object-id' => $tag_id,
			'menu-item-status' => 'publish'
		) );

		$cat_insert = wp_update_nav_menu_item( $this->menu_id, 0, array(
			'menu-item-type' => 'taxonomy',
			'menu-item-object' => 'category',
			'menu-item-object-id' => $cat_id,
			'menu-item-status' => 'publish'
		) );

		$post_insert = wp_update_nav_menu_item( $this->menu_id, 0, array(
			'menu-item-type' => 'post_type',
			'menu-item-object' => 'post',
			'menu-item-object-id' => $post_id,
			'menu-item-status' => 'publish'
		) );

		// Item without menu-item-object arg
		$post_2_insert = wp_update_nav_menu_item( $this->menu_id, 0, array(
			'menu-item-type' => 'post_type',
			'menu-item-object-id' => $post_2_id,
			'menu-item-status' => 'publish'
		) );

		$page_insert = wp_update_nav_menu_item( $this->menu_id, 0, array(
			'menu-item-type' => 'post_type',
			'menu-item-object' => 'page',
			'menu-item-object-id' => $page_id,
			'menu-item-status' => 'publish'
		) );

		$tag_items = wp_get_associated_nav_menu_items( $tag_id, 'taxonomy', 'post_tag' );
		$this->assertEqualSets( array( $tag_insert ), $tag_items );
		$cat_items = wp_get_associated_nav_menu_items( $cat_id, 'taxonomy', 'category' );
		$this->assertEqualSets( array( $cat_insert ), $cat_items );
		$post_items = wp_get_associated_nav_menu_items( $post_id );
		$this->assertEqualSets( array( $post_insert ), $post_items );
		$post_2_items = wp_get_associated_nav_menu_items( $post_2_id );
		$this->assertEqualSets( array( $post_2_insert ), $post_2_items );
		$page_items = wp_get_associated_nav_menu_items( $page_id );
		$this->assertEqualSets( array( $page_insert ), $page_items );

		wp_delete_term( $tag_id, 'post_tag' );
		$tag_items = wp_get_associated_nav_menu_items( $tag_id, 'taxonomy', 'post_tag' );
		$this->assertEqualSets( array(), $tag_items );

		wp_delete_term( $cat_id, 'category' );
		$cat_items = wp_get_associated_nav_menu_items( $cat_id, 'taxonomy', 'category' );
		$this->assertEqualSets( array(), $cat_items );

		wp_delete_post( $post_id, true );
		$post_items = wp_get_associated_nav_menu_items( $post_id );
		$this->assertEqualSets( array(), $post_items );

		wp_delete_post( $post_2_id, true );
		$post_2_items = wp_get_associated_nav_menu_items( $post_2_id );
		$this->assertEqualSets( array(), $post_2_items );

		wp_delete_post( $page_id, true );
		$page_items = wp_get_associated_nav_menu_items( $page_id );
		$this->assertEqualSets( array(), $page_items );
	}
}
