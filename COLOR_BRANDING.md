# Color Branding Guide

## Primary Brand Colors

### Main Colors
- **Primary Blue**: `#055498`
  - Used for: Primary buttons, links, headers, icons, main brand elements
  - RGB: `rgb(5, 84, 152)`
  
- **Secondary Blue**: `#123a60`
  - Used for: Secondary elements, gradients, hover states
  - RGB: `rgb(18, 58, 96)`
  
- **Accent Red**: `#CE2028`
  - Used for: Resolutions calendar events, error states, important alerts
  - RGB: `rgb(206, 32, 40)`
  - Border: `#a01a1f`

### Calendar Event Colors

- **Announcements (Yellow)**: `#FBD116`
  - Used for: Announcement calendar events
  - Border: `#d4a017`
  - RGB: `rgb(251, 209, 22)`
  
- **Resolutions (Red)**: `#CE2028`
  - Used for: Board Resolutions calendar events
  - Border: `#a01a1f`
  - RGB: `rgb(206, 32, 40)`
  
- **Regulations (Blue)**: `#055498`
  - Used for: Board Regulations calendar events
  - Border: `#044080`
  - RGB: `rgb(5, 84, 152)`
  
- **Notices (Purple)**: `#7C3AED`
  - Used for: Notices calendar events
  - Border: `#6D28D9`
  - RGB: `rgb(124, 58, 237)`

### Background Colors

- **Light Background**: `#F9FAFB`
  - Used for: Main page backgrounds (light mode)
  - RGB: `rgb(249, 250, 251)`
  
- **Dark Background**: `#0F172A`
  - Used for: Main page backgrounds (dark mode)
  - RGB: `rgb(15, 23, 42)`
  
- **Card Background (Light)**: `#FFFFFF`
  - Used for: Card backgrounds, modals (light mode)
  
- **Card Background (Dark)**: `#1e293b`
  - Used for: Card backgrounds, modals (dark mode)
  - RGB: `rgb(30, 41, 59)`

### Text Colors

- **Primary Text (Light Mode)**: `#0A0A0A`
  - Used for: Main text content (light mode)
  - RGB: `rgb(10, 10, 10)`
  
- **Primary Text (Dark Mode)**: `#F1F5F9`
  - Used for: Main text content (dark mode)
  - RGB: `rgb(241, 245, 249)`
  
- **Secondary Text (Light Mode)**: `#374151`
  - Used for: Secondary text, labels (light mode)
  - RGB: `rgb(55, 65, 81)`
  
- **Secondary Text (Dark Mode)**: `#9CA3AF`
  - Used for: Secondary text, labels (dark mode)
  - RGB: `rgb(156, 163, 175)`

### Gradient Combinations

- **Primary Gradient**: `linear-gradient(135deg, #055498 0%, #123a60 100%)`
  - Used for: Buttons, banners, hero sections
  - Direction: 135 degrees (diagonal)
  
- **Animated Gradient**: `linear-gradient(135deg, #055498 0%, #123a60 50%, #055498 100%)`
  - Used for: Animated backgrounds, banners
  - Background size: `200% 200%` (for animation)

### Status Colors

- **Success/Green**: Typically uses Tailwind's green palette
- **Warning/Yellow**: `#FBD116` (same as Announcements)
- **Error/Red**: `#CE2028` (same as Resolutions)
- **Info/Blue**: `#055498` (Primary Blue)

### Border Colors

- **Light Mode Borders**: `#E5E7EB` (gray-200)
- **Dark Mode Borders**: `#374151` (gray-700)
- **Primary Border**: `#044080` (darker blue for Primary Blue elements)
- **Accent Borders**: 
  - Yellow: `#d4a017`
  - Red: `#a01a1f`
  - Purple: `#6D28D9`

### Hover States

- **Primary Blue Hover**: `#123a60` (Secondary Blue)
- **Secondary Blue Hover**: `#055498` (Primary Blue)
- **Link Hover**: `#055498` (Primary Blue)

### Special Use Cases

- **Today's Date Highlight**: `rgba(5, 84, 152, 0.25)` (Primary Blue with 25% opacity)
- **Today Button**: `#FBD116` (Yellow) with text color `#123a60`
- **Neon Glow Effect**: `rgba(5, 84, 152, 0.5)` and `rgba(5, 84, 152, 0.3)`
- **Shadow Colors**: `rgba(5, 84, 152, 0.2)` to `rgba(5, 84, 152, 0.6)`

## Color Usage Guidelines

### Primary Blue (#055498)
- Main brand color
- Use for: Buttons, links, headers, icons, primary actions
- Avoid using for: Large background areas (use gradients instead)

### Secondary Blue (#123a60)
- Complementary brand color
- Use for: Gradients, hover states, secondary elements
- Works well with Primary Blue in gradients

### Accent Colors
- **Yellow (#FBD116)**: Announcements, highlights, today's date button
- **Red (#CE2028)**: Resolutions, errors, warnings
- **Purple (#7C3AED)**: Notices, special indicators

### Text Contrast
- Ensure WCAG AA contrast ratios:
  - Primary Blue on white: ✅ Good contrast
  - White on Primary Blue: ✅ Good contrast
  - Secondary text should maintain readability

## Implementation Notes

- All colors are defined inline in Blade templates using `style` attributes
- No CSS variables are currently used (consider migrating for easier maintenance)
- Dark mode colors are handled via Tailwind's `dark:` prefix
- Calendar event colors are hardcoded in JavaScript event rendering
- Gradient animations use CSS keyframes with `background-position` animation

## Color Palette Summary

```
Primary Colors:
  - Primary Blue:    #055498
  - Secondary Blue:  #123a60

Accent Colors:
  - Yellow:          #FBD116 (Announcements)
  - Red:             #CE2028 (Resolutions)
  - Purple:          #7C3AED (Notices)

Backgrounds:
  - Light:           #F9FAFB
  - Dark:            #0F172A
  - Card Light:      #FFFFFF
  - Card Dark:       #1e293b

Text:
  - Light Mode:      #0A0A0A
  - Dark Mode:       #F1F5F9
  - Secondary:       #374151 / #9CA3AF
```

