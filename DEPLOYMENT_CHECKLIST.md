# Azure Deployment Checklist & Setup Guide

## Pre-Deployment Checklist

### Azure Account Setup
- [ ] Azure account created (https://azure.microsoft.com/free/)
- [ ] Azure subscription ID obtained
- [ ] Azure CLI installed (`az --version`)
- [ ] Azure Dev CLI installed (`azd version`)
- [ ] Logged in to Azure (`az login`)

### GitHub Configuration (for CI/CD)
- [ ] Repository pushed to GitHub
- [ ] GitHub repository access token created
- [ ] Azure Service Principal credentials configured (see below)
- [ ] GitHub environment secrets configured

### Local Development
- [ ] PHP 8.2+ installed locally
- [ ] MySQL Server running locally
- [ ] Composer installed
- [ ] Database schema imported (`database.sql`)
- [ ] Application tested locally at http://localhost

## Azure Service Principal Setup (for CI/CD)

To enable GitHub Actions to deploy to Azure, create a service principal:

```bash
# Create service principal
$sp = az ad sp create-for-rbac `
  --name "TravelGo-GitHub-Deploy" `
  --role contributor `
  --scope "/subscriptions/<SUBSCRIPTION_ID>" `
  --json-auth

# This will output JSON with:
# - clientId
# - clientSecret  
# - subscriptionId
# - tenantId
```

**Add these as GitHub Secrets:**
- `AZURE_CLIENT_ID` → clientId
- `AZURE_TENANT_ID` → tenantId  
- `AZURE_SUBSCRIPTION_ID` → subscriptionId
- `AZURE_CLIENT_SECRET` → clientSecret
- `MYSQL_ADMIN_PASSWORD` → Your secure MySQL password

## Deployment Methods

### Method 1: Azure Dev CLI (Recommended - One Command)

```bash
# 1. Navigate to project
cd d:\xampp\htdocs\travel-website

# 2. Initialize (one-time)
azd init --template

# 3. Create environment
azd env new travelgo-prod

# 4. Configure
azd env set AZURE_LOCATION eastus
azd env set MYSQL_ADMIN_PASSWORD "StrongPassword123!"

# 5. One command to provision and deploy!
azd up
```

**That's it!** azd will:
- Create resource group
- Deploy infrastructure (App Service, MySQL, Key Vault, etc.)
- Deploy your application
- Configure everything automatically

### Method 2: Manual with Azure CLI

```bash
# Step 1: Create resource group
$rgName = "rg-travelgo"
az group create --name $rgName --location eastus

# Step 2: Deploy infrastructure
az deployment group create `
  --resource-group $rgName `
  --template-file infra/main.bicep `
  --parameters infra/main.parameters.json `
  --parameters environmentName=travelgo `
  --parameters location=eastus `
  --parameters mysqlAdminPassword="StrongPassword123!"

# Step 3: Get App Service name
$appName = az deployment group show `
  --name main `
  --resource-group $rgName `
  --query properties.outputs.appServiceName.value -o tsv

# Step 4: Package application
Compress-Archive -Path ./* -DestinationPath application.zip -Force

# Step 5: Deploy code
az webapp deployment source config-zip `
  --resource-group $rgName `
  --name $appName `
  --src ./application.zip

# Step 6: Configure App Service
az webapp config appsettings set `
  --resource-group $rgName `
  --name $appName `
  --settings `
    MYSQL_PASSWORD="StrongPassword123!" `
    MYSQL_HOST="${appName}-mysql.mysql.database.azure.com" `
    MYSQL_USER=azureuser `
    MYSQL_DATABASE=travel_db
```

### Method 3: GitHub Actions (Automated CI/CD)

Once set up, every push to `main` branch automatically deploys!

```bash
# 1. Configure GitHub Secrets (Settings → Secrets)
# Add: AZURE_CLIENT_ID, AZURE_TENANT_ID, AZURE_SUBSCRIPTION_ID, MYSQL_ADMIN_PASSWORD

# 2. Push to GitHub
git push origin main

# 3. Watch deployment in Actions tab
# GitHub Actions will automatically provision and deploy!
```

## Post-Deployment Steps

### 1. Verify Application is Running

```bash
# Get the application URL
$appName = "app-<your-unique-token>"
$url = "https://$(az webapp show --resource-group rg-travelgo --name $appName --query defaultHostName -o tsv)"

# Test it
Start-Process $url
```

### 2. Import Database Schema

You can import the database in 3 ways:

**Option A: Using Azure Portal**
1. Go to Azure Portal → Azure Database for MySQL
2. Click "Query Editor" → Connect
3. Run commands from `database.sql`

**Option B: Using MySQL CLI**
```bash
# Get MySQL server name
$mysqlServer = az deployment group show --name main --resource-group rg-travelgo --query properties.outputs.mysqlServerName.value -o tsv

# Connect and import
mysql -h ${mysqlServer}.mysql.database.azure.com -u azureuser -p -e "CREATE DATABASE travel_db;"
mysql -h ${mysqlServer}.mysql.database.azure.com -u azureuser -p travel_db < database.sql
```

**Option C: Using Azure CLI**
```bash
# Create database
az mysql db create --resource-group rg-travelgo --server-name $mysqlServer --name travel_db

# Note: For schema import, use Portal or MySQL CLI
```

### 3. Configure Firebase (if using)

Add Firebase credentials to App Service:

```bash
az webapp config appsettings set \
  --resource-group rg-travelgo \
  --name $appName \
  --settings \
    FIREBASE_API_KEY="your-key" \
    FIREBASE_AUTH_DOMAIN="your-domain.firebaseapp.com" \
    FIREBASE_PROJECT_ID="your-project" \
    FIREBASE_STORAGE_BUCKET="your-bucket"
```

### 4. Enable HTTPS Only

```bash
az webapp update \
  --resource-group rg-travelgo \
  --name $appName \
  --https-only
```

### 5. Configure Custom Domain (Optional)

```bash
# Add custom domain
az webapp config hostname add \
  --resource-group rg-travelgo \
  --webapp-name $appName \
  --hostname yourdomain.com

# Create SSL certificate
az webapp config ssl bind \
  --resource-group rg-travelgo \
  --name $appName \
  --certificate-thumbprint <thumbprint>
```

## Monitoring & Logging

### View Logs

```bash
# Real-time log streaming
az webapp log tail --resource-group rg-travelgo --name $appName

# Or in Azure Portal:
# App Service → Log stream
```

### Application Insights

Monitor performance and errors:

```bash
# Get insights connection string
az deployment group show \
  --name main \
  --resource-group rg-travelgo \
  --query properties.outputs
```

Then view in Azure Portal → Application Insights

### Database Monitoring

```bash
# View MySQL metrics
az monitor metrics list-definitions \
  --resource-group rg-travelgo \
  --resource-type "Microsoft.DBforMySQL/servers" \
  --resource <mysql-server-name>
```

## Troubleshooting

### Application Won't Start

```bash
# Check App Service logs
az webapp log tail --resource-group rg-travelgo --name $appName

# Common issues:
# 1. PHP error_log location not writable
# 2. Database connection failed
# 3. Missing PHP extensions
```

### Database Connection Failed

```bash
# 1. Verify MySQL server exists
az mysql server list --resource-group rg-travelgo

# 2. Check firewall rules
az mysql server firewall-rule list --resource-group rg-travelgo --server-name <mysql-name>

# 3. Verify connection string in App Service
az webapp config connection-string list --resource-group rg-travelgo --name $appName

# 4. Test connection locally with same credentials
mysql -h <mysql-server>.mysql.database.azure.com -u azureuser -p
```

### High Costs

To reduce Azure costs:

```bash
# Downgrade App Service tier (from B2 to B1)
az appservice plan update \
  --resource-group rg-travelgo \
  --name asp-<token> \
  --sku B1

# Downgrade MySQL (from Standard_B1ms to Standard_B1s)
az mysql server update \
  --resource-group rg-travelgo \
  --name mysql-<token> \
  --sku-name Standard_B1s
```

## Cost Estimation

**Monthly approximate costs (B2 App Service + Standard MySQL):**
- App Service (B2): ~$60
- Azure Database for MySQL: ~$100-150
- Application Insights: ~$15-30
- Key Vault: ~$0.50
- **Total: ~$175-240/month**

**To reduce costs, use B1 tier:** ~$80-120/month

## Cleanup (Stop Paying)

When done testing, delete everything:

```bash
# Using azd (if you used it)
azd down

# Or manually
az group delete --name rg-travelgo --yes
```

This removes all Azure resources and stops all charges.

## Security Checklist

✅ **Implemented in Deployment:**
- [ ] HTTPS enforcement
- [ ] Managed Identity authentication
- [ ] Key Vault for secrets
- [ ] MySQL SSL/TLS
- [ ] Firewall rules
- [ ] Application Insights monitoring
- [ ] Secure headers

✅ **Additional Recommended:**
- [ ] Configure WAF (Web Application Firewall)
- [ ] Enable Azure Policy compliance
- [ ] Set up backup policies
- [ ] Configure email alerts
- [ ] Enable Azure Defender
- [ ] Review access controls

## Success Indicators

After deployment, you should see:

✅ Application accessible at `https://<app-name>.azurewebsites.net`
✅ Database connected and accessible
✅ Logs visible in Azure Portal
✅ Application Insights showing requests
✅ Custom domain working (if configured)
✅ Email/contact forms working
✅ Firebase authentication working
✅ All PHP pages loading

## Next Steps

1. Test all application features in production
2. Set up backup strategy
3. Configure custom domain
4. Set up CI/CD for automatic deployments
5. Monitor costs and performance
6. Plan scaling strategy

## Support

- **Azure Documentation**: https://learn.microsoft.com/azure/
- **App Service PHP Guide**: https://learn.microsoft.com/azure/app-service/app-service-web-get-started-php
- **azd Documentation**: https://learn.microsoft.com/azure/developer/azure-developer-cli/
- **Troubleshooting**: https://learn.microsoft.com/azure/app-service/troubleshoot-common-app-service-errors
