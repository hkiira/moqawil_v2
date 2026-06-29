# Dashboard Design - Visual Reference Guide

## Color Palette Reference

### Primary Colors
```
Primary Blue-Purple:        #667eea (RGB: 102, 126, 234)
Primary Dark Purple:        #764ba2 (RGB: 118, 75, 162)
```

### Status Colors
```
Success (Teal):            #1BC5BD (RGB: 27, 197, 189)
Warning (Orange):          #FFA800 (RGB: 255, 168, 0)
Danger (Red):              #F64E60 (RGB: 246, 78, 96)
Info (Purple):             #8950FC (RGB: 137, 80, 252)
```

### Neutral Colors
```
Light Background:          #f8f9fa (RGB: 248, 249, 250)
White:                     #ffffff
Dark Gray (Sidebar):       #2c3e50 (RGB: 44, 62, 80)
Sidebar Accent:            #34495e (RGB: 52, 73, 94)
```

### Text Colors
```
Primary Text:              #1f2937 (RGB: 31, 41, 55)
Secondary Text:            #6b7280 (RGB: 107, 114, 128)
Muted Text:                #9ca3af (RGB: 156, 163, 175)
Light Text:                #ecf0f1 (RGB: 236, 240, 241)
```

---

## Component Showcase

### Stat Card Variants

#### Primary (Default)
```
┌─────────────────────────────────────────┐
│ TOTAL ORDERS                    📦      │
│                                         │
│ 2,456                                   │
│                                         │
│ Orders this month          +12.5%       │
└─────────────────────────────────────────┘
```
- Border: Purple
- Icon: Purple background
- Value: Purple text
- Usage: Main metrics

#### Success (Green)
```
┌─────────────────────────────────────────┐
│ REVENUE TOTAL                   💰      │
│                                         │
│ 156,800 DH                              │
│                                         │
│ Revenue this month         +8.2%        │
└─────────────────────────────────────────┘
```
- Border: Teal
- Icon: Teal background
- Value: Teal text
- Usage: Positive metrics

#### Warning (Orange)
```
┌─────────────────────────────────────────┐
│ PENDING ORDERS                  ⏳      │
│                                         │
│ 142                                     │
│                                         │
│ To be processed                         │
└─────────────────────────────────────────┘
```
- Border: Orange
- Icon: Orange background
- Value: Orange text
- Usage: Attention needed

#### Danger (Red)
```
┌─────────────────────────────────────────┐
│ FAILED ORDERS                   ❌      │
│                                         │
│ 12                                      │
│                                         │
│ Requires investigation                  │
└─────────────────────────────────────────┘
```
- Border: Red
- Icon: Red background
- Value: Red text
- Usage: Critical items

#### Info (Purple)
```
┌─────────────────────────────────────────┐
│ ACTIVE USERS                    👥      │
│                                         │
│ 1,240                                   │
│                                         │
│ Users online now           +5.3%        │
└─────────────────────────────────────────┘
```
- Border: Purple
- Icon: Purple background
- Value: Purple text
- Usage: Informational

---

## Layout Examples

### Single Metric Row
```
┌──────────────────────────────────────────────────────────────┐
│ TOTAL SALES          REVENUE        PENDING      CUSTOMERS    │
│ 2,456                156K DH        142          1,240        │
│ Orders this month    Revenue YTD    To process   Active users │
└──────────────────────────────────────────────────────────────┘
```

### Two Column Layout (Charts)
```
┌────────────────────────────────────────────────────────────────┐
│ Revenue Chart                  │ Orders Status Chart            │
│                                │                                │
│ [Chart visualization]          │ [Chart visualization]          │
│                                │                                │
├────────────────────────────────────────────────────────────────┤
```

### Three Column Layout (Performance)
```
┌─────────────────────────────────────────────────────────────────┐
│ Conversion | Low Stock | Delivery Rate                          │
│ 3.8%       | 28 items  | 94.2%                                 │
│ Progress ▓▓▓▓ 65%  Progress ▓ 20%  Progress ▓▓▓▓▓ 94%          │
├─────────────────────────────────────────────────────────────────┤
```

### Full Table Layout
```
┌─────────────────────────────────────────────────────────────────┐
│ Recent Orders                                                   │
├───────┬──────────┬────────┬──────────┬───────────┬────────────┤
│ Order │ Customer │ Amount │ Status   │ Date      │ Action     │
├───────┼──────────┼────────┼──────────┼───────────┼────────────┤
│ #001  │ Client A │ 2.5K   │ ✓ Shipped│ 2024-01-15│ View    │
│ #002  │ Client B │ 3.2K   │ ⏳ Pend │ 2024-01-16│ View    │
│ #003  │ Client C │ 1.8K   │ ℹ Conf  │ 2024-01-17│ View    │
└───────┴──────────┴────────┴──────────┴───────────┴────────────┘
```

---

## Typography Scale

### Headings
```
Dashboard Header H1:    28px, Bold (700), Letter-spacing: -0.5px
Section Title H2:       22px, Bold (700)
Card Title H5:          16px, Bold (700)
Stat Label:             12px, Bold (700), Uppercase
Stat Value:             36px, Bold (800)
```

### Body Text
```
Regular Paragraph:      14px, Regular (400)
Secondary Text:         13px, Regular (400), Color: #6b7280
Small Text:             12px, Regular (400), Color: #9ca3af
```

---

## Icon Usage Guide

### Business Icons
```
📦 Shopping         fa-shopping-cart
💰 Revenue/Money    fa-wallet
👥 Users/People     fa-users
📦 Products         fa-box / fa-boxes
🏭 Warehouse        fa-warehouse
🚚 Delivery/Truck   fa-truck
📊 Chart/Data       fa-chart-line
```

### Status Icons
```
✅ Success/Check    fa-check-circle
⏳ Pending/Wait     fa-hourglass-half
❌ Error/Failed     fa-times-circle
⚠️ Warning          fa-exclamation-triangle
ℹ️ Info             fa-info-circle
📋 List/Tasks       fa-list-check
```

### Directional Icons
```
📈 Up/Growth        fa-arrow-up
📉 Down/Decline     fa-arrow-down
➡️ Next/Forward     fa-arrow-right
⬅️ Previous/Back    fa-arrow-left
🔄 Refresh/Sync     fa-sync
🔍 Search           fa-search
```

---

## Badge/Status Display

### Status Badges
```
✓ Success           Badge: badge-success (Green/Teal)
⏳ Pending          Badge: badge-warning (Orange)
ℹ️ Information      Badge: badge-info (Light Blue)
🚫 Danger/Failed    Badge: badge-danger (Red)
● Active/Primary    Badge: badge-primary (Purple)
```

### Badge Examples
```
[✓ Completed] [⏳ In Progress] [ℹ️ Confirmed] [❌ Cancelled] [● Active]
```

---

## Spacing & Sizing

### Card Spacing
```
Padding Inside Card:    25px
Margin Between Cards:   20px
Border Radius:          10px
Icon Size:              60px
Icon Border Radius:     10px
```

### Typography Spacing
```
H1 to Subheading:       8px
Subheading to Content:  12px
Paragraph Margin:       15px
List Item Margin:       10px
```

### Responsive Breakpoints
```
Mobile:    < 576px     (1 column, full width)
Tablet:    576-992px   (2 columns)
Desktop:   992px+      (4 columns)
```

---

## Hover & Interaction States

### Stat Card Hover
```
Before:     Default position, subtle shadow
Hover:      Lift up by 5px, enhanced shadow, smooth transition
```

### Button Hover
```
Before:     Outline, colored text
Hover:      Filled background, white text, lift effect
Active:     Filled background, no lift
```

### Table Row Hover
```
Before:     Light gray border
Hover:      Light purple background (5% opacity)
```

### Icon Hover
```
Before:     Static
Hover:      Scale up to 110%, smooth transition
```

---

## Animation Reference

### Fade In
```
Timing:     400ms ease-in
Effect:     Opacity 0→1, Transform translateY(20px→0)
Use for:    Page/content load
```

### Slide Down
```
Timing:     500ms ease
Effect:     Opacity 0→1, Transform translateY(-20px→0)
Use for:    Header
```

### Hover Lift
```
Timing:     300ms ease
Effect:     Transform translateY(0→-5px)
Use for:    Cards, buttons
```

### Smooth Transitions
```
Timing:     300ms ease
Effect:     All property changes
Use for:    Hover states, color changes
```

---

## Dashboard Layout Structure

```
┌───────────────────────────────────────────────────────────────┐
│ Dashboard Header (Gradient)                                   │
│ Title: "Tableau de Bord"                                     │
│ Subtitle: "Overview of your activity"                        │
└───────────────────────────────────────────────────────────────┘

┌───────┬───────┬───────┬───────────────────────────────────────┐
│ Card1 │ Card2 │ Card3 │ Card4                                │
└───────┴───────┴───────┴───────────────────────────────────────┘

┌─────────────────────────────────┬─────────────────────────────┐
│ Chart Card 1                    │ Chart Card 2                │
│ (Revenue/Orders)                │ (Status/Distribution)       │
├─────────────────────────────────┴─────────────────────────────┤
│ Performance Metrics (3 columns)                               │
├─────────────────────────────────────────────────────────────────┤
│ Data Table (Full Width)                                      │
├─────────────────────────────────────────────────────────────────┤
│ Quick Actions (Full Width)                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## Code Color Reference (CSS)

```css
/* Primary Colors */
--primary-color: #667eea;
--primary-dark: #764ba2;

/* Status Colors */
--success-color: #1BC5BD;
--warning-color: #FFA800;
--danger-color: #F64E60;
--info-color: #8950FC;

/* Neutral Colors */
--light-bg: #f8f9fa;
--sidebar-bg: #2c3e50;
--sidebar-accent: #34495e;
--border-color: #ecf0f1;
```

---

## Mobile vs Desktop Comparison

### Desktop View (4 Cards)
```
┌─────┬─────┬─────┬─────┐
│ C1  │ C2  │ C3  │ C4  │
└─────┴─────┴─────┴─────┘
```

### Tablet View (2 Cards)
```
┌────────┬────────┐
│ C1     │ C2     │
├────────┼────────┤
│ C3     │ C4     │
└────────┴────────┘
```

### Mobile View (1 Card)
```
┌──────────┐
│ C1       │
├──────────┤
│ C2       │
├──────────┤
│ C3       │
├──────────┤
│ C4       │
└──────────┘
```

---

## Quick Copy-Paste Reference

### Stat Card HTML
```html
<div class="stat-card primary">
    <div class="stat-card-label">Card Title</div>
    <div class="stat-card-value">1,234</div>
    <div class="stat-card-desc">Description text</div>
    <div class="stat-card-icon">
        <i class="fas fa-icon"></i>
    </div>
</div>
```

### Chart Card HTML
```html
<div class="chart-card">
    <h5 class="chart-card-title">Title</h5>
    <div id="chart" style="height: 300px;"></div>
</div>
```

### Table HTML
```html
<table class="table table-custom">
    <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Data 1</td>
            <td>Data 2</td>
        </tr>
    </tbody>
</table>
```

---

## Design Principles

### 1. Visual Hierarchy
- Large values draw attention
- Colors guide importance
- Spacing creates grouping

### 2. Consistency
- Same components, same style
- Consistent spacing throughout
- Unified color usage

### 3. Accessibility
- Good color contrast
- Clear labels
- Readable font sizes

### 4. Responsiveness
- Mobile-first approach
- Flexible layouts
- Touch-friendly sizing

### 5. Performance
- Smooth animations
- Fast load times
- Optimized images

---

## Print-Friendly Reference

**Primary Colors:**
- Purple: #667eea
- Dark Purple: #764ba2

**Status Colors:**
- Green: #1BC5BD
- Orange: #FFA800
- Red: #F64E60
- Purple: #8950FC

**Card Padding:** 25px  
**Border Radius:** 10px  
**Icon Size:** 60px  
**Value Font:** 36px Bold  
**Label Font:** 12px Bold Uppercase  

---

**Design Version:** 1.0  
**Last Updated:** January 3, 2026  
**Status:** Production Ready
