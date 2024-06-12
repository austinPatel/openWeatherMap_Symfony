## OpenWeather Api Using Symfony 7
# Install Scoop
# Install Symfony-cli
## Clone Repository to your local machine
# git clone https://github.com/austinPatel/openWeatherMap_Symfony.git
# Switch Branch to Master
# Composer Install
# Execute Migration file to generate schema in your database.
# change the database configuration based on your enviornment of database server details
# PHP Version 8.*
# Symfony 7
# To used OpenWeather Map API need to created Free account and copy the api key into .env file

# OpenWeather api used lat and lon to fetch and store the Weather data
# API Endpoint - http://127.0.0.1:8000/weather
# Description - To fetch and store the weather data by lat and lon or by city name
# Method - Get 
# Request Body -Json
# Example 1
{
"lat": "44.34",
"lon":"10.99"
}
# Example 2
{
    "city": "london"
}

## API endpoint - http://127.0.0.1:8000/weather/search
# Description - To search by city or fetchAt with get the result of weather data which is store in database
# Method - Get
# 
# Request Body- Json
{
    "city": "London"
}