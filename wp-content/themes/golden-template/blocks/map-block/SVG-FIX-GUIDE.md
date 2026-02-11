# SVG Map Fix Guide

## Problem

Your current SVG file is a single `<path>` element without the required region IDs. The JavaScript needs to find specific elements with IDs to enable interactivity.

## Solution

You need to restructure your SVG to include **group elements (`<g>`)** or separate **path elements** with the following IDs:

1. `map-region-dfw-houston`
2. `map-region-austin`
3. `map-region-metro-dc`
4. `map-region-boston`
5. `map-region-phoenix`
6. `map-region-atlanta`

## How to Fix

### Option 1: Wrap Paths in Groups (Recommended)

If your SVG has separate paths for each region, wrap them in `<g>` elements:

```xml
<svg width="1165" height="719" viewBox="0 0 1165 719" fill="none" xmlns="http://www.w3.org/2000/svg">
  <!-- DFW/Houston Region -->
  <g id="map-region-dfw-houston">
    <!-- All paths/pixels for DFW/Houston area -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
    <!-- More paths for this region -->
  </g>
  
  <!-- Austin Region -->
  <g id="map-region-austin">
    <!-- All paths/pixels for Austin area -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  
  <!-- Metro DC Region -->
  <g id="map-region-metro-dc">
    <!-- All paths/pixels for Metro DC area -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  
  <!-- Boston Region -->
  <g id="map-region-boston">
    <!-- All paths/pixels for Boston area -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  
  <!-- Phoenix Region -->
  <g id="map-region-phoenix">
    <!-- All paths/pixels for Phoenix area -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
  
  <!-- Atlanta Region -->
  <g id="map-region-atlanta">
    <!-- All paths/pixels for Atlanta area -->
    <path d="..." fill="#22211A" fill-opacity="0.2"/>
  </g>
</svg>
```

### Option 2: Use Single Path with Overlay Elements

If you can't separate the paths, you can add invisible overlay rectangles/paths on top:

```xml
<svg width="1165" height="719" viewBox="0 0 1165 719" fill="none" xmlns="http://www.w3.org/2000/svg">
  <!-- Your existing map path -->
  <path d="..." fill="#22211A" fill-opacity="0.2"/>
  
  <!-- Invisible overlay regions for interactivity -->
  <g id="map-region-dfw-houston">
    <rect x="X_COORD" y="Y_COORD" width="WIDTH" height="HEIGHT" fill="transparent" opacity="0"/>
  </g>
  
  <g id="map-region-austin">
    <rect x="X_COORD" y="Y_COORD" width="WIDTH" height="HEIGHT" fill="transparent" opacity="0"/>
  </g>
  
  <!-- Repeat for other regions -->
</svg>
```

### Option 3: Use Path Elements with IDs

If you have separate paths, just add IDs directly:

```xml
<path id="map-region-dfw-houston" d="..." fill="#22211A" fill-opacity="0.2"/>
<path id="map-region-austin" d="..." fill="#22211A" fill-opacity="0.2"/>
<!-- etc. -->
```

## Testing

After updating your SVG:

1. Open the browser console (F12)
2. Look for any warnings about missing map regions
3. Hover over a location card - the corresponding map region should highlight
4. Click on a location card - the map region should become active (blue)

## Current SVG Structure

Your current SVG has:
- One single `<path>` element with all pixels
- No region IDs

## Required SVG Structure

Your SVG needs:
- Six separate elements (groups or paths) with the exact IDs listed above
- Each element should cover the geographic area for that location

## Tools to Help

1. **SVG Editor**: Use Inkscape, Adobe Illustrator, or online tools like SVG-Edit
2. **Manual Editing**: Open the SVG in a text editor and wrap/group paths
3. **Coordinate Mapping**: Use the viewBox coordinates to identify which pixels belong to which region

## Example Structure

Here's a minimal working example:

```xml
<svg class="jlb-map-block__map" width="1165" height="719" viewBox="0 0 1165 719" fill="none" xmlns="http://www.w3.org/2000/svg">
  <!-- Background map (all pixels) -->
  <path d="M0 293.306H17.0033V276.303H0V293.306Z..." fill="#22211A" fill-opacity="0.2"/>
  
  <!-- Interactive regions (invisible overlays or grouped paths) -->
  <g id="map-region-dfw-houston" style="cursor: pointer;">
    <!-- Paths for Texas DFW/Houston area -->
  </g>
  
  <g id="map-region-austin" style="cursor: pointer;">
    <!-- Paths for Austin area -->
  </g>
  
  <g id="map-region-metro-dc" style="cursor: pointer;">
    <!-- Paths for Metro DC area -->
  </g>
  
  <g id="map-region-boston" style="cursor: pointer;">
    <!-- Paths for Boston area -->
  </g>
  
  <g id="map-region-phoenix" style="cursor: pointer;">
    <!-- Paths for Phoenix area -->
  </g>
  
  <g id="map-region-atlanta" style="cursor: pointer;">
    <!-- Paths for Atlanta area -->
  </g>
</svg>
```

## Important Notes

- **IDs must match exactly**: `map-region-dfw-houston` (not `map-region-dfw` or `dfw-houston`)
- **Case sensitive**: IDs are case-sensitive
- **No spaces**: Use hyphens, not spaces
- **Unique IDs**: Each region must have a unique ID
