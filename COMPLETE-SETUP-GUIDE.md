# ✅ Golden Template Complete Setup Guide

## 🎯 **CRITICAL: ACF Pro Required**

Your Golden Template theme uses a **Must-Use Plugin** called **"Golden Template Core Loader"** that manages dependencies. This plugin **REQUIRES** Advanced Custom Fields PRO to be active.

---

## 📋 **Setup Checklist**

### **Step 1: Activate ACF Pro** ⚠️ **REQUIRED**

1. Go to WordPress Admin: `/wp-admin`
2. Navigate to: **Plugins > Installed Plugins**
3. Find: **Advanced Custom Fields PRO**
4. Click: **Activate**

**Status Check:**
- ✅ If ACF Pro is active: Error notice disappears
- ❌ If not active: Red error notice from "Golden Template Core Loader"

---

### **Step 2: Verify Block Registration**

After activating ACF Pro:

1. **Refresh WordPress admin** (Ctrl+Shift+R)
2. **Go to Pages > Add New**
3. **Click "+" button**
4. **Look for these blocks:**

#### **In "Golden Template Components" Category:**
- **"Hero Section"** - Full ACF-powered hero block
- **"Simple Hero (No ACF)"** - Fallback that always works

#### **What You Should See:**
```
Golden Template Components
├── Hero Section (with ACF fields)
└── Simple Hero (No ACF) (fallback)
```

---

## 🏗️ **Component Structure**

Your theme uses a **component-based architecture**:

```
/blocks/hero-section/
├── fields.php          ← ACF field definitions
├── template.php        ← HTML/PHP template
├── hero-section.scss   ← Component styles
└── hero-section.css    ← Compiled CSS
```

---

## 🔧 **How It Works**

### **1. Golden Template Core Loader (Must-Use Plugin)**
- **Location**: `/wp-content/mu-plugins/golden-template-core-loader.php`
- **Purpose**: Manages theme dependencies
- **Checks**: ACF Pro, Yoast SEO
- **Shows**: Error notices if required plugins are missing

### **2. Block Registration**
- **Location**: `/inc/blocks/block-registration.php`
- **Registers**: Hero Section block with ACF
- **Fallback**: Simple Hero block (no ACF needed)

### **3. ACF Fields**
- **Location**: `/blocks/hero-section/fields.php`
- **Fields**: Background Image, Title, Content, Repeater Items
- **Loads**: Automatically via functions.php

### **4. Template Rendering**
- **Location**: `/blocks/hero-section/template.php`
- **Renders**: Block output on frontend
- **Styles**: Uses inline styles + hero-section.css

---

## 🎨 **Using the Hero Section Block**

### **After ACF Pro is Active:**

1. **Go to Pages > Add New**
2. **Click "+" button**
3. **Search for "hero" or browse "Golden Template Components"**
4. **Select "Hero Section"**
5. **Fill in the fields:**
   - **Background Image**: Upload an image
   - **Title**: Enter your hero title (required)
   - **Content**: Add description text
   - **Test Items**: Add repeater items (optional)
6. **Preview/Publish** the page

---

## 🚨 **Troubleshooting**

### **Issue: No blocks showing**
**Solution:**
1. Check if ACF Pro is activated
2. Look for red error notice from "Golden Template Core Loader"
3. Activate ACF Pro in Plugins page

### **Issue: "Simple Hero (No ACF)" shows but not "Hero Section"**
**Solution:**
- ACF Pro is not properly activated
- Check Plugins page and activate ACF Pro

### **Issue: ACF Pro license warning**
**Solution:**
- ACF Pro works without license for development
- "Simple Hero (No ACF)" will work as fallback
- For full features, enter license in Custom Fields > Updates

### **Issue: Blocks registered but fields don't show**
**Solution:**
- Clear browser cache (Ctrl+Shift+R)
- Check if fields.php is being loaded
- Look for PHP errors in debug.log

---

## 📊 **Debug Information**

### **Check What's Loaded:**

The debug notice shows:
- ✅ ACF Pro Functions Available
- ✅ ACF Fields Functions Available  
- ✅ ACF Pro License Status
- ✅ Hero Block Registration Status
- ✅ Available Custom Blocks

### **Expected Debug Output (When Working):**

```
🔍 Golden Template Block Debug Status
┌─────────────────────────────┬──────────┐
│ ACF Pro Functions           │ ✅ YES   │
│ ACF Fields Functions        │ ✅ YES   │
│ ACF Pro License             │ Valid    │
│ Hero Block (ACF)            │ ✅ Registered │
│ Hero Block (Fallback)       │ ✅ Registered │
│ Total Blocks                │ 50+      │
└─────────────────────────────┴──────────┘

Available Custom Blocks: acf/hero-section, golden-template/simple-hero
```

---

## 🎯 **Quick Start (TL;DR)**

1. **Activate ACF Pro** in Plugins
2. **Refresh WordPress admin**
3. **Go to Pages > Add New**
4. **Click "+" and search "hero"**
5. **Add "Hero Section" block**
6. **Fill in fields and publish**

---

## 📁 **File Locations**

### **Core Files:**
- Must-Use Plugin: `/wp-content/mu-plugins/golden-template-core-loader.php`
- Theme Functions: `/wp-content/themes/golden-template/functions.php`
- Block Registration: `/wp-content/themes/golden-template/inc/blocks/block-registration.php`

### **Hero Section Component:**
- Fields: `/wp-content/themes/golden-template/blocks/hero-section/fields.php`
- Template: `/wp-content/themes/golden-template/blocks/hero-section/template.php`
- Styles: `/wp-content/themes/golden-template/blocks/hero-section/hero-section.scss`

### **Debug Files (Temporary):**
- Debug Script: `/wp-content/themes/golden-template/debug-blocks.php`
- (Remove after testing)

---

## ✅ **Success Criteria**

You'll know everything is working when:

1. ✅ No red error notices in WordPress admin
2. ✅ "Golden Template Components" category appears in block inserter
3. ✅ "Hero Section" block is available
4. ✅ ACF fields show when you add the block
5. ✅ Block renders correctly on frontend
6. ✅ Styles load properly

---

## 🚀 **Next Steps**

Once the hero-section is working:

1. **Create more components** using the same structure
2. **Style components** with SCSS
3. **Build pages** using your components
4. **Test on frontend** to verify everything works

---

## 📞 **Current Status**

**What's Set Up:**
- ✅ Golden Template Core Loader (must-use plugin)
- ✅ Block registration system
- ✅ Hero section component (fields, template, styles)
- ✅ Fallback blocks for development
- ✅ Debug system

**What You Need to Do:**
- ⚠️ **Activate ACF Pro plugin**
- ⚠️ **Refresh WordPress admin**
- ⚠️ **Test the hero block**

**Once ACF Pro is active, your hero-section block will appear in the block editor!**