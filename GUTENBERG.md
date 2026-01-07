# Gutenberg Block Integration (Future Enhancement)

## Overview

Currently, the plugin uses **shortcodes** to display the booking calendar and form. This is the simplest and most compatible approach that works with all themes.
Archive and single templates for inflatables are already provided by the plugin, with optional theme overrides.

If you want to add Gutenberg blocks in the future for better integration, here's how to do it:

## Creating Gutenberg Blocks

### Option 1: Convert Shortcodes to Blocks (Easy)

WordPress automatically converts shortcodes to blocks! This means users can use:

1. **Classic Editor block** → Enter shortcode
2. **Shortcode block** → Enter shortcode directly

Example:
```
[hoptown_booking_calendar inflatable_id="123"]
```

### Option 2: Create Custom Gutenberg Blocks (Advanced)

For true Gutenberg integration, you need to create custom blocks.

#### Step 1: Add Block Registration

Add to `includes/admin/class-admin.php`:

```php
public function register_gutenberg_blocks() {
    // Check if Gutenberg is available
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }

    wp_register_script(
        'hoptown-rental-blocks',
        HOPTOWN_RENTAL_PLUGIN_URL . 'assets/js/blocks.js',
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
        HOPTOWN_RENTAL_VERSION
    );

    register_block_type(
        'hoptown-rental/booking-calendar',
        array(
            'editor_script'   => 'hoptown-rental-blocks',
            'render_callback' => array( $this, 'render_booking_calendar_block' ),
            'attributes'      => array(
                'inflatableId' => array(
                    'type'    => 'number',
                    'default' => 0,
                ),
            ),
        )
    );
}

public function render_booking_calendar_block( $attributes ) {
    $inflatable_id = isset( $attributes['inflatableId'] ) ? intval( $attributes['inflatableId'] ) : 0;

    if ( ! $inflatable_id ) {
        return '<p>Please select an inflatable.</p>';
    }

    return do_shortcode( '[hoptown_booking_calendar inflatable_id="' . $inflatable_id . '"]' ) .
           do_shortcode( '[hoptown_booking_form inflatable_id="' . $inflatable_id . '"]' );
}
```

#### Step 2: Create Block JavaScript

Create `assets/js/blocks.js`:

```javascript
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;
const { __ } = wp.i18n;

registerBlockType('hoptown-rental/booking-calendar', {
    title: __('Hoptown Booking Calendar', 'hoptown-rental'),
    icon: 'calendar-alt',
    category: 'widgets',
    attributes: {
        inflatableId: {
            type: 'number',
            default: 0
        }
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Settings', 'hoptown-rental')}>
                        <TextControl
                            label={__('Inflatable ID', 'hoptown-rental')}
                            value={attributes.inflatableId}
                            onChange={(value) => setAttributes({ inflatableId: parseInt(value) })}
                            type="number"
                        />
                    </PanelBody>
                </InspectorControls>

                <div className="hoptown-block-placeholder">
                    <p>📅 Hoptown Booking Calendar</p>
                    <p>Inflatable ID: {attributes.inflatableId || 'Not set'}</p>
                </div>
            </>
        );
    },

    save: function() {
        return null; // Dynamic block
    }
});
```

#### Step 3: Register Block Hook

Add to `includes/class-hoptown-rental.php`:

```php
private function define_admin_hooks() {
    // ... existing code ...

    add_action( 'init', array( $plugin_admin, 'register_gutenberg_blocks' ) );
}
```

### Option 3: Create Block with Inflatable Selector (Most Advanced)

For the best UX, create a block with a dropdown to select inflatables:

```javascript
const { SelectControl } = wp.components;
const { useSelect } = wp.data;

edit: function(props) {
    const { attributes, setAttributes } = props;

    // Fetch inflatables
    const inflatables = useSelect((select) => {
        return select('core').getEntityRecords('postType', 'hoptown_inflatable');
    });

    const options = inflatables ? inflatables.map(inflatable => ({
        label: inflatable.title.rendered,
        value: inflatable.id
    })) : [];

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Settings', 'hoptown-rental')}>
                    <SelectControl
                        label={__('Select Inflatable', 'hoptown-rental')}
                        value={attributes.inflatableId}
                        options={[
                            { label: __('Select...', 'hoptown-rental'), value: 0 },
                            ...options
                        ]}
                        onChange={(value) => setAttributes({ inflatableId: parseInt(value) })}
                    />
                </PanelBody>
            </InspectorControls>

            <div className="hoptown-block-placeholder">
                <p>📅 Hoptown Booking Calendar</p>
                {attributes.inflatableId ? (
                    <p>Selected: {options.find(o => o.value === attributes.inflatableId)?.label}</p>
                ) : (
                    <p>Please select an inflatable</p>
                )}
            </div>
        </>
    );
}
```

## Implementation Strategy

### Current Setup (Recommended for now)
- Uses shortcodes - works with all themes
- Simple to implement
- No JavaScript build process needed
- Works with Classic and Block editor

### Future Enhancement (Gutenberg Blocks)
- Better editor experience
- Visual preview in editor
- Easier for non-technical users
- Requires JavaScript build process

## When to Implement Gutenberg Blocks

Consider adding Gutenberg blocks when:
1. Basic functionality is stable and tested
2. Client is comfortable with current shortcode implementation
3. You have time for enhancement
4. You want to improve UX for content editors

## Build Process for Blocks (If Implementing)

If you decide to create custom blocks, you'll need:

1. **Install Node.js and npm**

2. **Create package.json**:
```json
{
  "name": "hoptown-rental-blocks",
  "version": "1.0.0",
  "scripts": {
    "build": "wp-scripts build",
    "start": "wp-scripts start"
  },
  "devDependencies": {
    "@wordpress/scripts": "^26.0.0"
  }
}
```

3. **Install dependencies**:
```bash
npm install
```

4. **Create src/blocks.js** (move blocks.js here)

5. **Build**:
```bash
npm run build
```

6. **Update script registration** to use built file:
```php
HOPTOWN_RENTAL_PLUGIN_URL . 'build/blocks.js'
```

## Resources

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [Create Block Tutorial](https://developer.wordpress.org/block-editor/getting-started/create-block/)
- [@wordpress/scripts](https://www.npmjs.com/package/@wordpress/scripts)

## Alternative: Block Patterns

An even simpler option is to create **Block Patterns** that use shortcodes:

```php
register_block_pattern(
    'hoptown-rental/booking-pattern',
    array(
        'title'       => __('Hoptown Booking', 'hoptown-rental'),
        'description' => __('Add a booking calendar and form', 'hoptown-rental'),
        'content'     => '<!-- wp:shortcode -->[hoptown_booking_calendar inflatable_id=""]<!-- /wp:shortcode -->
                         <!-- wp:shortcode -->[hoptown_booking_form inflatable_id=""]<!-- /wp:shortcode -->',
        'categories'  => array('widgets'),
    )
);
```

This allows users to quickly insert a template with shortcodes!

---

**Conclusion**: For now, shortcodes are completely sufficient and the best option. You can add Gutenberg blocks later as an enhancement without changing the existing code.
