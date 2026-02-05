# Simple Sendy SES Bridge 📧

> **Connect WordPress with Sendy & Amazon SES to create beautiful newsletters in seconds.**

![License](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)
![Version](https://img.shields.io/badge/version-1.0.0-green.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.9%2B-blue.svg)
Tested up to: 6.9

**Simple Sendy SES Bridge** is a powerful WordPress plugin that bridges the gap between your content and your subscribers. Say goodbye to copy-pasting content into Sendy. Select your posts, choose a layout, and send campaigns directly from your WP dashboard.

## 🚀 Features

*   **🔌 Seamless Integration**: Connects directly to your self-hosted Sendy installation via API.
*   **📰 Content Selector**: Search and select any WordPress post to add to your newsletter.
*   **✨ Custom Template**: Enforced "Hero + Grid" layout designed for engagement:
    *   **Hero Section**: Highlights your primary story.
    *   **Grid Layout**: Displays subsequent stories in a clean 2-column grid.
    *   **Dynamic Footer**: Global settings for Logo, Copyright, and Social Links.
*   **🖼️ Banner Support**: Add a custom header image from your Media Library.
*   **📋 Saved Lists**: Pre-configure your Sendy lists in settings for easy selection (supports sending to multiple lists).
*   **📱 Mobile Optimized**: All layouts and images are optimized for mobile devices.
*   **🗓️ Scheduled Sending**: Schedule your newsletters to be sent at a specific date and time.
*   **🧪 Test Emails**: Send test emails to yourself to verify the design (requires SMTP plugin).
*   **🔗 Smart Linking**: Featured images are automatically linked to your articles.

## 🛠️ Installation

1.  **Upload** the `simple-sendy-ses-bridge` folder to your `/wp-content/plugins/` directory.
2.  **Activate** the plugin through the 'Plugins' menu in WordPress.
3.  **Navigate** to **Sendy Bridge > Settings** to configure your connection.

## ⚙️ Configuration

You'll need the following from your Sendy installation:

*   **Installation URL**: Your Sendy URL (e.g., `https://sendy.yourdomain.com`).
*   **API Key**: Found in your Sendy Settings.

### 📝 Saved Lists (Optional)
Instead of typing a List ID every time, you can save your commonly used lists in **Settings**:
*   Enter them one per line: `List Name, List ID`
*   Example:
    ```
    Main Subscribers, l123abc
    Test Group, t456def
    All Lists|l123abc,t456def
    ```

### 👣 Footer & Social Settings
Customize your newsletter footer globally:
*   **Footer Logo**: URL to your logo image.
*   **Copyright Text**: Supports `{year}` placeholder (e.g. `© {year} My Site`).
*   **Social Links**: Add your Instagram, LinkedIn, X (Twitter), or YouTube URLs. Icons appear automatically.
*   **"Read More" Link**: Adds a button at the bottom pointing to your blog.



## ☕ Support

If this plugin helps you, please consider buying me a coffee!

<a href="https://buymeacoffee.com/gunjanjaswal" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" height="40" width="145"></a>

## 📬 Contact

*   **Email**: [hello@gunjanjaswal.me](mailto:hello@gunjanjaswal.me)
*   **Website**: [gunjanjaswal.me](https://gunjanjaswal.me)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is licensed under the GPL-2.0+ License.
