# Taro Taxonomy Blocks

Tags: gutenberg, block editor, iframe  
Contributors: tarosky, Takahashi_Fumiki  
Tested up to: 5.8  
Requires at least: 5.4  
Requires PHP: 5.6  
Stable Tag: nightly  
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Add 3 term blocks.

## Description

This plugin supports 3 term blocks.

1. **Terms Block** - Display all terms in the specified taxonomy. Usefull to display terms list like glossary.
2. **Post's Terms Block** - Display terms assigned to the post in the specified taxonomy.
3. **Post's Terms Query Block** - Display post list with same terms with the post.

### Customization

#### Template Structure

To override look and feel, put template in your themes directory.

```
template-parts
- taxonomy-blocks
  - posts-list.php             // List of post in post's terms query blocks. 
  - post-loop.php              // Post link in post's terms query blocks. 
  - term-item.php              // Term link.
  - term-list.php              // Flat term list.
  - term-list-hierarchical.php // Hierarchical terms list.
```

`taro_taxonomy_blocks_template` filter hook is also available.
This will override the template file path.

#### Styles

To override styles, regsiter styels named `taro-terms-block`.
The plugin registers style at priority 20 of `init` hook, so registering style at priority 10.

```
add_action( 'init', function() {
    // Your own CSS.
    wp_register_style( 'taro-terms-block', $your_block_css_url, $deps, $version );
} );
```

Now your blocks will be styled by your CSS.

## Installation

### From Plugin Repository

Click install and activate it.

### From Github

See [releases](https://github.com/tarosky/taro-taxonomy-blocks/releases).

## FAQ

### Where can I get supported?

Please create new ticket on support forum.

### How can I contribute?

Create a new [issue](https://github.com/tarosky/taro-taxonomy-blocks/issues) or send [pull requests](https://github.com/tarosky/taro-taxonomy-blocks/pulls).

## Changelog

### 1.1.0

* Post terms query block accepts manually input term list.

### 1.0.0

* First release.
