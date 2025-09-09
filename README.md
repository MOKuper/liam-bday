# 🎉 Liam's Birthday Party Website

A fun and interactive birthday party invitation and RSVP system built with Laravel, Docker, Nginx, and MySQL. Perfect for managing children's birthday parties with personalized guest experiences!

## 🎈 Features

- **Personalized Guest Pages**: Each guest gets a unique URL with their name
- **RSVP System**: Track who's coming with attendee counts and dietary restrictions
- **Birthday Guestbook**: Guests can leave messages, drawings, photos, and audio recordings
- **Admin Dashboard**: Manage guests, view RSVPs, approve messages, and update party details
- **Kid-Friendly Design**: Colorful, animated UI with emojis and fun fonts
- **Countdown Timer**: Shows days until the party
- **Mobile Responsive**: Works great on phones and tablets

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose installed
- Port 8080 available on your machine

### Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd bday-liam
   ```

2. **Start the Docker containers**
   ```bash
   make up
   # or
   docker-compose up -d
   ```

3. **Run database migrations and seeders**
   ```bash
   make fresh
   # or
   docker-compose exec php php artisan migrate:fresh --seed
   ```

4. **Access the application**
   - Home page: http://localhost:8080
   - Admin dashboard: http://localhost:8080/admin

## 📧 Sample Guest Invitation URLs

The seeder creates 4 sample guests with unique invitation links:

- **Emma Thompson**: http://localhost:8080/invite/[unique-code]
- **Oliver Martinez**: http://localhost:8080/invite/[unique-code]
- **Sophia Chen**: http://localhost:8080/invite/[unique-code]
- **Noah Williams**: http://localhost:8080/invite/[unique-code]

To get the actual URLs with unique codes:
```bash
docker-compose exec php php artisan tinker
>>> App\Models\Guest::all(['name', 'unique_url'])->map(fn($g) => $g->name . ': http://localhost:8080/invite/' . $g->unique_url);
```

## 🎮 How to Use

### For Party Hosts (Admin)

1. **Access Admin Dashboard**: Go to http://localhost:8080/admin

2. **Add Guests**:
   - Click "Manage Guests"
   - Fill in guest details (name, parent info if child)
   - System generates unique invitation URL
   - Copy and send the URL to guests

3. **Track RSVPs**:
   - View real-time RSVP status
   - See total attendee counts
   - Check dietary restrictions

4. **Manage Messages**:
   - Review birthday messages
   - Approve/hide messages for public guestbook
   - View uploaded photos, drawings, and audio

5. **Update Party Details**:
   - Edit date, time, venue
   - Update activities and theme
   - Add gift suggestions

### For Guests

1. **Open Personal Invitation**: Use the unique URL sent by the host

2. **RSVP**:
   - Select attending status
   - Specify number of adults and children
   - Add dietary restrictions or special needs

3. **Leave Birthday Message**:
   - Write a personal message
   - Upload a drawing or photo
   - Record an audio message

4. **View Party Details**:
   - See date, time, and location
   - Check parking information
   - View activities and theme
   - See gift suggestions

## 🛠 Development Commands

### Using Make (recommended)
```bash
make up              # Start all containers
make down            # Stop all containers
make migrate         # Run migrations
make fresh           # Fresh migrate with seeders
make seed            # Run seeders
make artisan [cmd]   # Run any artisan command
make composer [cmd]  # Run composer commands
make shell           # Access PHP container shell
```

### Using Docker Compose directly
```bash
# Start services
docker-compose up -d

# Run migrations
docker-compose exec php php artisan migrate

# Create new guest
docker-compose exec php php artisan tinker
>>> App\Models\Guest::create(['name' => 'New Guest', 'is_child' => true]);

# View logs
docker-compose logs -f php
```

## 📁 Project Structure

```
bday-liam/
├── app/
│   ├── Http/Controllers/     # Guest, RSVP, Message, Admin controllers
│   └── Models/              # Guest, RSVP, Message, PartyDetail models
├── database/
│   ├── migrations/          # Database structure
│   └── seeders/            # Sample data
├── resources/
│   └── views/              # Blade templates
│       ├── layouts/        # Base layout
│       ├── guest/          # Guest pages
│       └── admin/          # Admin pages
├── routes/
│   └── web.php            # Application routes
├── docker/
│   ├── nginx/             # Nginx configuration
│   └── php/               # PHP Dockerfile
├── docker-compose.yml      # Docker services
└── Makefile               # Convenience commands
```

## 🎨 Customization

### Change Party Details
Update the seeder or use the admin panel:
- Child's name and age
- Party date and time
- Venue information
- Theme and activities

### Modify Styles
The app uses Tailwind CSS via CDN. Edit views in `resources/views/` to change:
- Colors and fonts
- Animations
- Layout structure

### Add Features
Common additions:
- Email notifications
- QR codes for invitations
- Photo gallery
- Games or activities
- Gift registry integration

## 🐛 Troubleshooting

### Container Issues
```bash
# Rebuild containers
docker-compose build --no-cache

# View container logs
docker-compose logs [service-name]

# Reset everything
docker-compose down -v
docker-compose up -d
```

### Database Issues
```bash
# Reset database
make fresh

# Check migration status
docker-compose exec php php artisan migrate:status
```

### Permission Issues
```bash
# Fix storage permissions
docker-compose exec php chmod -R 777 storage bootstrap/cache
```

## 🔒 Security Notes

This is a demo application. For production use:
- Add authentication to admin routes
- Use environment variables for sensitive data
- Enable HTTPS
- Add rate limiting
- Implement file upload validation
- Add CAPTCHA for public forms

## 📝 License

This project is open source and available under the MIT License.

---

Made with ❤️ for Liam's 5th Birthday! 🎂🦕
