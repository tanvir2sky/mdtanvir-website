# MD Tanvir Hossain - Personal Website

A modern, single-page personal website for MD Tanvir Hossain, a Software Engineer specializing in Laravel, PHP, and Shopify development.

## 🌐 Live Website

Visit: [mdtanvir.com](https://mdtanvir.com)

## ✨ Features

- **Modern Design**: Minimal, premium, and professional aesthetic
- **Developer-Centric**: Clean typography with subtle animations
- **Fully Responsive**: Mobile-first design that works on all devices
- **Dark/Light Mode**: Toggle between themes with persistent preference
- **Smooth Scrolling**: Seamless navigation between sections
- **Section Transitions**: Elegant fade-in animations on scroll
- **SEO Optimized**: Semantic HTML with proper meta tags
- **Accessibility**: Focus states and keyboard navigation support

## 🛠️ Tech Stack

- **HTML5**: Semantic markup
- **TailwindCSS**: Utility-first CSS framework (via CDN)
- **Vanilla JavaScript**: No heavy frameworks, pure JS
- **Font Awesome**: Icon library
- **Modern CSS**: Flexbox and Grid layouts

## 📁 Project Structure

```
mdtanvir/
├── index.html      # Main HTML file
├── styles.css      # Custom CSS styles
├── script.js       # JavaScript functionality
└── README.md       # Project documentation
```

## 🚀 Getting Started

1. **Clone or download** this repository
2. **Open `index.html`** in a web browser
3. That's it! No build process required.

### Local Development

Simply open `index.html` in your browser or use a local server:

```bash
# Using Python
python -m http.server 8000

# Using Node.js (http-server)
npx http-server

# Using PHP
php -S localhost:8000
```

Then visit `http://localhost:8000` in your browser.

## 📝 Sections

1. **Hero Section**: Name, role, tagline, and call-to-action buttons
2. **About Me**: Professional summary and background
3. **Skills**: Categorized technical skills (Backend, Frontend, E-commerce, Tools)
4. **Experience**: Timeline-style professional experience
5. **Projects**: Featured projects with descriptions and tech stacks
6. **Contact**: Email, LinkedIn, and GitHub links

## 🎨 Customization

### Colors

The website uses a primary blue color scheme. To change colors, modify the Tailwind config in `index.html`:

```javascript
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: {
          // Your color palette
        },
      },
    },
  },
};
```

### Content

Update the content in `index.html`:

- Personal information in the Hero section
- About Me text
- Skills list
- Experience entries
- Project details
- Contact information

### Theme

The dark/light mode toggle is automatically handled. User preference is saved in localStorage.

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🔧 Performance

- Lightweight: No heavy frameworks or dependencies
- Fast loading: CDN resources with fallbacks
- Optimized animations: CSS-based for smooth performance
- Lazy loading ready: Structure supports lazy loading if images are added

## 📄 License

This project is personal and proprietary. All rights reserved.

## 👤 Author

**MD Tanvir Hossain**

- LinkedIn: [tanvir-cs](https://www.linkedin.com/in/tanvir-cs)
- GitHub: [tanvir-cs](https://github.com/tanvir-cs)
- Email: contact@mdtanvir.com

---

Built with ❤️ using TailwindCSS and vanilla JavaScript.
