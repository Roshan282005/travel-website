# TravelGo - Azure Deployment Guide

## Prerequisites

Before deploying to Azure, ensure you have:

1. **Azure CLI** - [Install Azure CLI](https://learn.microsoft.com/cli/azure/install-azure-cli)
2. **Azure Dev CLI (azd)** - [Install azd](https://learn.microsoft.com/azure/developer/azure-developer-cli/install-azd)
3. **Azure Subscription** - [Create free account](https://azure.microsoft.com/free/)
4. **Git** - For version control

## Quick Start Deployment (Recommended)

### Step 1: Initialize Azure Dev Project

```bash
# Navigate to project directory
cd d:\xampp\htdocs\travel-website

# Authenticate with Azure
az login

# Initialize Azure Developer CLI
azd init --template

# Choose or create an environment name (e.g., "travelgo-dev")
azd env new
```

### Step 2: Set Configuration

```bash
# Set your Azure subscription
azd env set AZURE_SUBSCRIPTION_ID <your-subscription-id>

# Set location (e.g., eastus, westeurope, southeastasia)
azd env set AZURE_LOCATION eastus

# Set MySQL password (strong password required!)
azd env set MYSQL_ADMIN_PASSWORD "YourSecurePassword123!"
```

### Step 3: Deploy Infrastructure

```bash
# Provision Azure resources (creates resource group, App Service, MySQL, etc.)
azd provision
```

This will:
- Create a resource group
- Deploy App Service (Linux PHP 8.2)
- Deploy Azure Database for MySQL
- Create Key Vault for secrets
- Configure networking and firewalls
- Set up Application Insights monitoring

### Step 4: Deploy Application

```bash
# Deploy your application code
azd deploy
```

### Step 5: Initialize Database

```bash
# Get the App Service URL
$appUrl = azd env get-values | Select-String 'appServiceUrl' | ForEach-Object { $_.Line.Split('=')[1].Trim() }

# Upload database schema
# You'll need to import the database.sql file through Azure Portal MySQL blade or via command line:

az mysql db create --resource-group <rg-name> --server-name <mysql-name> --name travel_db

# Import the database schema (using mysql command-line tool if available):
mysql -h <mysql-server>.mysql.database.azure.com -u azureuser@<mysql-server> -p travel_db < database.sql
```

## Manual Deployment (Alternative)

If you prefer manual deployment without azd:

### Step 1: Create Resource Group

```bash
$rgName = "rg-travelgo"
$location = "eastus"

az group create --name $rgName --location $location
```

### Step 2: Deploy Infrastructure (Bicep)

```bash
az deployment group create `
  --resource-group $rgName `
  --template-file infra/main.bicep `
  --parameters infra/main.parameters.json `
  --parameters environmentName=travelgo-prod `
  --parameters location=$location `
  --parameters mysqlAdminPassword="YourSecurePassword123!"
```

### Step 3: Deploy Code

```bash
# Using Git deployment
$appName = "app-<unique-token>"

az webapp deployment source config-zip `
  --resource-group $rgName `
  --name $appName `
  --src ./application.zip
```

## Configuration After Deployment

### 1. Set MySQL Password in App Service

After deployment, the MySQL password must be set in App Service settings:

```bash
az webapp config appsettings set `
  --resource-group $rgName `
  --name $appName `
  --settings MYSQL_PASSWORD="YourSecurePassword123!"
```

### 2. Configure Firebase (if using Firebase Authentication)

In Azure App Service Settings, add:
```
FIREBASE_API_KEY=your_firebase_key
FIREBASE_AUTH_DOMAIN=your_firebase_domain
```

### 3. Enable HTTPS Only

```bash
az webapp update `
  --resource-group $rgName `
  --name $appName `
  --https-only true
```

## Monitoring & Logs

### View Application Logs

```bash
# Stream logs in real-time
az webapp log tail --resource-group $rgName --name $appName

# Or via Azure Portal:
# App Service → Monitoring → Log stream
```

### Application Insights

Monitor your application performance:
- Visit Azure Portal
- Navigate to Application Insights resource
- View performance metrics, errors, and requests

## Troubleshooting

### Database Connection Issues

1. **Check MySQL Connection String:**
   ```bash
   az webapp config connection-string show --resource-group $rgName --name $appName
   ```

2. **Verify Firewall Rules:**
   ```bash
   az mysql server firewall-rule list --resource-group $rgName --server-name <mysql-name>
   ```

3. **Check App Service Logs:**
   - Azure Portal → App Service → Monitoring → Log stream

### Database Import Issues

If database schema import fails:

```bash
# Connect directly to MySQL
mysql -h <mysql-name>.mysql.database.azure.com -u azureuser -p

# Then run SQL commands from database.sql file
```

## Cleanup

When done testing, remove Azure resources to avoid charges:

```bash
# Using azd
azd down

# Or manually
az group delete --name $rgName --yes
```

## Security Best Practices

✅ **Implemented:**
- HTTPS enforcement
- Managed Identity for Azure service authentication
- Key Vault for secrets storage
- SSL/TLS for MySQL connections
- Azure Firewall rules
- Application Insights monitoring

✅ **Recommended Additional Steps:**
- Configure WAF (Web Application Firewall)
- Enable Azure DDoS Protection
- Set up backup policies
- Configure custom domain with SSL certificate

## Support & Documentation

- [Azure App Service Documentation](https://learn.microsoft.com/azure/app-service/)
- [Azure Database for MySQL](https://learn.microsoft.com/azure/mysql/)
- [Azure Dev CLI Documentation](https://learn.microsoft.com/azure/developer/azure-developer-cli/)
- [PHP on Azure](https://learn.microsoft.com/azure/app-service/app-service-web-get-started-php)

## Next Steps

1. Test your application at `https://<app-name>.azurewebsites.net`
2. Configure custom domain (optional)
3. Set up CI/CD pipeline for automated deployments
4. Configure email notifications for alerts
5. Plan backup and disaster recovery strategy
