# 📧 Simple Sendy SES Bridge

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg) ![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-aaaaaa.svg) ![License](https://img.shields.io/badge/license-GPLv2-green.svg)

**Connect WordPress to Sendy with style.**
Create beautiful, responsive newsletters directly from your WordPress posts and send them via your Sendy installation (Amazon SES).

---

## ✨ Features

### 🎨 Visual Newsletter Builder
*   **Hero + Grid Layout:** Automatically formats your first selected post as a featured "Hero" card, and subsequent posts as a neat **2-column grid** (on desktop).
*   **Live Preview:** See exactly what your email will look like as you add posts.

### 📱 Mobile-First Responsive Design
*   **Smart Stacking:** Grid items automatically switch to a **single-column layout** on mobile devices (< 600px).
*   **Auto-Height:** Cards adjust their height dynamically to fit content, removing awkward whitespace on small screens.
*   **Readable Typography:** Optimized font sizes and spacing for mobile reading.

### 🖼️ Professional Media Handling
*   **Integrated Banners:** Banner images are fully integrated into the Hero card with rounded corners.
*   **No-Crop Scaling:** Banners use `height: auto` to ensure 100% of your image content is visible (no heads or text cut off).
*   **High-Res Thumbnails:** Uses the 'Large' image size to prevent blurriness on retina screens.

### 🛠️ Powerful Admin Tools
*   **Instant Search:** AJAX search bar to find any post in your database.
*   **Campaign Management:** 
    *   **Status Columns:** View `Draft`, `Sent`, or `Scheduled` status directly in the campaign list.
    *   **Scheduling:** Set a date/time for your campaign, and WP-Cron will trigger the send automatically.
    *   **Status Indicators:** Color-coded statuses (Green for Sent, Orange for Scheduled).
    *   **Error Handling:** Failed campaigns show detailed error messages in a dedicated column.
    *   **One-Click Retry:** Instantly retry failed campaigns with a single button click.
    *   **Overdue Alerts:** Automatic warnings for scheduled campaigns that didn't send (WP-Cron issues).

### 🏷️ Keywords
`Sendy` `Amazon SES` `Newsletter Builder` `Email Marketing` `WordPress Newsletter` `Drag and Drop` `Post to Email` `Responsive Email Template` `Email Automation` `Campaign Scheduler`

---

## 🚀 Installation

1.  **Download** the plugin zip file.
2.  **Upload** to your WordPress site via *Plugins > Add New > Upload Plugin*.
3.  **Activate** the plugin.
4.  **Configure** your Sendy details in *Settings > Simple Sendy Bridge*:
    *   Sendy Installation URL
    *   API Key
    *   Default List ID

## 📖 Usage

1.  Navigate to **Simple Sendy Bridge > Create Newsletter**.
2.  **Select a Banner:** Choose an image from your Media Library.
3.  **Add Posts:** Search for your articles and click "Add".
    *   1st Post = **Hero**
    *   2nd+ Posts = **Grid**
4.  **Preview:** Check the preview column.
5.  **Send or Schedule:** Choose to Send Now, Save as Draft (in Sendy), or Schedule for later.

---

## 🤝 Support

Created by **Gunjan Jaswal**.
If you like this plugin, consider [Buying Me A Coffee ☕](https://buymeacoffee.com/gunjanjaswal).
