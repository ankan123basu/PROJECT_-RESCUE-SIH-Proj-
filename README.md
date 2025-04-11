# Rescue Agency Coordination System

A comprehensive web application for coordinating rescue agencies during natural and man-made calamities. The system provides real-time location tracking, resource management, and incident response coordination.

## Features

- **Real-time Agency Tracking**: View locations of all registered rescue agencies on an interactive map
- **Resource Management**: Track and manage various rescue resources (ambulances, helicopters, fire trucks, etc.)
- **Incident Response**: Coordinate emergency response during calamities
- **Agency Registration**: Easy registration process for rescue agencies
- **Real-time Updates**: Live updates of agency status and incident reports
- **Interactive Maps**: Multiple map views for different resource types
- **Responsive Design**: Works seamlessly on desktop and mobile devices

## Technologies Used

- **Frontend**:
  - HTML5
  - Tailwind CSS
  - JavaScript
  - MapBox GL JS

- **Backend**:
  - PHP
  - MySQL
  - WebSocket (for real-time updates)

## Setup Instructions

1. Clone the repository
2. Set up a local web server (e.g., XAMPP, WAMP)
3. Create a MySQL database named 'rescue_system'
4. Update the database credentials in `php/config.php`
5. Get a MapBox API key and update it in `dashboard.html`
6. Access the application through your local web server

## Database Structure

The system uses three main tables:
- `agencies`: Stores rescue agency information
- `incidents`: Records emergency incidents
- `resource_assignments`: Tracks resource assignments to incidents

## API Endpoints

- `/php/login.php`: Handle user authentication
- `/php/register.php`: Process agency registration
- `/php/get_agencies.php`: Retrieve agency locations and details

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Security Considerations

- All passwords are hashed before storage
- API keys should be stored securely
- Input validation is implemented for all forms
- CSRF protection is in place
- SQL injection prevention through prepared statements

## Future Enhancements

- Mobile app integration
- Advanced analytics dashboard
- Weather integration
- Resource optimization algorithms
- Communication system integration
- Automated dispatch system
