# Map Block - Setup Instructions

## SVG Map File Required

This block requires an SVG map file with specific region IDs. The SVG file should be placed at:

**Path:** `/wp-content/themes/jlbpartners/assets/images/map-usa.svg`

## Required SVG Structure

The SVG map **MUST** include the following region IDs for interactivity. **Your current SVG is missing these IDs!**

### Current Problem

Your SVG file has a single `<path>` element without region IDs. The JavaScript cannot find the regions to enable interactivity.

### Required Structure

The SVG map must include **group elements (`<g>`)** or **path elements** with these exact IDs:

```xml
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1165 719">
  <!-- Option 1: Group elements (recommended) -->
  <g id="map-region-dfw-houston">
    <!-- All paths/pixels for DFW/Houston region -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  <g id="map-region-austin">
    <!-- All paths/pixels for Austin region -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  <g id="map-region-metro-dc">
    <!-- All paths/pixels for Metro DC region -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  <g id="map-region-boston">
    <!-- All paths/pixels for Boston region -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  <g id="map-region-phoenix">
    <!-- All paths/pixels for Phoenix region -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  <g id="map-region-atlanta">
    <!-- All paths/pixels for Atlanta region -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
</svg>
```

**See `SVG-FIX-GUIDE.md` for detailed instructions on how to fix your SVG file.**

## Map Region IDs

The following IDs must be present in the SVG:

1. `map-region-dfw-houston` - DFW/Houston region
2. `map-region-austin` - Austin region
3. `map-region-metro-dc` - Metro DC region
4. `map-region-boston` - Boston region
5. `map-region-phoenix` - Phoenix region
6. `map-region-atlanta` - Atlanta region

## How It Works

1. **Map Regions**: Each region in the SVG should have an ID matching the pattern `map-region-{location-key}`
2. **Card Association**: Each card has a `data-map-region` attribute that matches the SVG region ID
3. **Interactivity**: JavaScript handles hover and click events to highlight corresponding regions and cards

## Styling

Map regions can be styled using CSS:

```css
/* Default state */
#map-region-dfw-houston {
  fill: #e0e0e0;
}

/* Hover state */
.jlb-map-block__region--highlighted {
  fill: #00a400;
  opacity: 0.7;
}

/* Active state */
.jlb-map-block__region--active {
  fill: #0073aa;
  opacity: 1;
}
```

## Notes

- The SVG map is **not manageable via CMS** - it's a static file
- Map regions are **hardcoded** and cannot be added/removed through the editor
- All 6 location cards must be populated in the editor
- Cards are linked to map regions via `data-map-region` attribute
