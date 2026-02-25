metadata description = 'Creates Azure resources for TravelGo PHP application'
param environmentName string
param location string = resourceGroup().location
param resourceToken string = uniqueString(subscription().id, resourceGroup().id, location, environmentName)
param tags object = {
  'azd-env-name': environmentName
  'created': utcNow('u')
}

// App Service resources
param appServicePlanSkuName string = 'B2'
param phpVersion string = '8.2'

// MySQL Database resources
param mysqlAdminUsername string = 'azureuser'
@secure()
param mysqlAdminPassword string
param mysqlSkuName string = 'Standard_B1ms'
param mysqlStorageGB int = 20

// Key Vault
param keyVaultName string = 'kv${resourceToken}'

// Application Insights
var appInsightsName = 'appi-${resourceToken}'
var appServicePlanName = 'asp-${resourceToken}'
var appServiceName = 'app-${resourceToken}'
var mysqlServerName = 'mysql-${resourceToken}'
var userAssignedIdentityName = 'uami-${resourceToken}'

// User-Assigned Managed Identity
resource userAssignedIdentity 'Microsoft.ManagedIdentity/userAssignedIdentities@2023-01-31' = {
  name: userAssignedIdentityName
  location: location
  tags: tags
}

// Application Insights
resource appInsights 'Microsoft.Insights/components@2020-02-02' = {
  name: appInsightsName
  location: location
  kind: 'web'
  properties: {
    Application_Type: 'web'
    RetentionInDays: 30
    publicNetworkAccessForIngestion: 'Enabled'
    publicNetworkAccessForQuery: 'Enabled'
  }
  tags: tags
}

// Key Vault
resource keyVault 'Microsoft.KeyVault/vaults@2023-07-01' = {
  name: keyVaultName
  location: location
  properties: {
    tenantId: subscription().tenantId
    sku: {
      family: 'A'
      name: 'standard'
    }
    accessPolicies: []
    publicNetworkAccess: 'Enabled'
    enableRbacAuthorization: true
  }
  tags: tags
}

// Key Vault Secret - MySQL Connection String
resource mysqlConnectionStringSecret 'Microsoft.KeyVault/vaults/secrets@2023-07-01' = {
  parent: keyVault
  name: 'mysql-connection-string'
  properties: {
    value: 'Server=${mysqlServerName}.mysql.database.azure.com;Port=3306;Database=travel_db;Uid=${mysqlAdminUsername};Pwd=${mysqlAdminPassword};'
  }
}

// Key Vault Secret - MySQL Admin Password
resource mysqlPasswordSecret 'Microsoft.KeyVault/vaults/secrets@2023-07-01' = {
  parent: keyVault
  name: 'mysql-admin-password'
  properties: {
    value: mysqlAdminPassword
  }
}

// Role Assignment - Key Vault Secrets User
resource kvSecretsUserRole 'Microsoft.Authorization/roleAssignments@2022-04-01' = {
  scope: keyVault
  name: guid(keyVault.id, userAssignedIdentity.id, 'b86a8fe4-44ce-4948-aee5-eccb2c155cd7')
  properties: {
    roleDefinitionId: '/subscriptions/${subscription().subscriptionId}/providers/Microsoft.Authorization/roleDefinitions/b86a8fe4-44ce-4948-aee5-eccb2c155cd7'
    principalId: userAssignedIdentity.properties.principalId
    principalType: 'ServicePrincipal'
  }
}

// App Service Plan
resource appServicePlan 'Microsoft.Web/serverfarms@2023-01-01' = {
  name: appServicePlanName
  location: location
  kind: 'linux'
  sku: {
    name: appServicePlanSkuName
    capacity: 1
  }
  properties: {
    reserved: true
  }
  tags: tags
}

// App Service
resource appService 'Microsoft.Web/sites@2023-01-01' = {
  name: appServiceName
  location: location
  kind: 'app,linux,container'
  identity: {
    type: 'UserAssigned'
    userAssignedIdentities: {
      '${userAssignedIdentity.id}': {}
    }
  }
  properties: {
    serverFarmId: appServicePlan.id
    siteConfig: {
      linuxFxVersion: 'PHP|${phpVersion}'
      alwaysOn: true
      http20Enabled: true
      minTlsVersion: '1.2'
      cors: {
        allowedOrigins: [
          '*'
        ]
        supportCredentials: false
      }
      appSettings: [
        {
          name: 'APPLICATIONINSIGHTS_CONNECTION_STRING'
          value: appInsights.properties.ConnectionString
        }
        {
          name: 'MYSQL_HOST'
          value: '${mysqlServerName}.mysql.database.azure.com'
        }
        {
          name: 'MYSQL_USER'
          value: mysqlAdminUsername
        }
        {
          name: 'MYSQL_DATABASE'
          value: 'travel_db'
        }
        {
          name: 'KEY_VAULT_URL'
          value: keyVault.properties.vaultUri
        }
        {
          name: 'WEBSITE_RUN_FROM_PACKAGE'
          value: '1'
        }
      ]
      connectionStrings: [
        {
          name: 'DefaultConnection'
          connectionString: 'Server=${mysqlServerName}.mysql.database.azure.com;Port=3306;Database=travel_db;Uid=${mysqlAdminUsername};Pwd=${mysqlAdminPassword};'
          type: 'MySql'
        }
      ]
    }
    httpsOnly: true
  }
  tags: tags
  dependsOn: [
    kvSecretsUserRole
  ]
}

// Diagnostic Settings for App Service
resource appServiceDiagnostics 'Microsoft.Insights/diagnosticSettings@2021-05-01-preview' = {
  name: 'diag-${resourceToken}'
  scope: appService
  properties: {
    workspaceId: '/subscriptions/${subscription().subscriptionId}/resourceGroups/${resourceGroup().name}/providers/Microsoft.OperationalInsights/workspaces/log-${resourceToken}'
    logs: [
      {
        category: 'AppServiceConsoleLogs'
        enabled: true
        retentionPolicy: {
          enabled: true
          days: 7
        }
      }
      {
        category: 'AppServiceHTTPLogs'
        enabled: true
        retentionPolicy: {
          enabled: true
          days: 7
        }
      }
    ]
    metrics: [
      {
        category: 'AllMetrics'
        enabled: true
        retentionPolicy: {
          enabled: true
          days: 7
        }
      }
    ]
  }
}

// MySQL Server
resource mysqlServer 'Microsoft.DBforMySQL/servers@2017-12-01' = {
  name: mysqlServerName
  location: location
  sku: {
    name: mysqlSkuName
    tier: 'GeneralPurpose'
    capacity: 2
    family: 'Gen5'
  }
  properties: {
    createMode: 'Default'
    version: '5.7'
    administratorLogin: mysqlAdminUsername
    administratorLoginPassword: mysqlAdminPassword
    storageProfile: {
      storageMB: mysqlStorageGB * 1024
      backupRetentionDays: 7
      geoRedundantBackup: 'Disabled'
      storageAutogrow: 'Enabled'
    }
    sslEnforcement: 'ENABLED'
  }
  tags: tags
}

// MySQL Firewall Rule - Allow Azure Services
resource mysqlFirewallRule 'Microsoft.DBforMySQL/servers/firewallRules@2017-12-01' = {
  parent: mysqlServer
  name: 'AllowAzureServices'
  properties: {
    startIpAddress: '0.0.0.0'
    endIpAddress: '0.0.0.0'
  }
}

// MySQL Firewall Rule - Allow App Service
resource mysqlAppServiceFirewallRule 'Microsoft.DBforMySQL/servers/firewallRules@2017-12-01' = {
  parent: mysqlServer
  name: 'AllowAppService'
  properties: {
    startIpAddress: '0.0.0.0'
    endIpAddress: '255.255.255.255'
  }
}

// MySQL Database
resource mysqlDatabase 'Microsoft.DBforMySQL/servers/databases@2017-12-01' = {
  parent: mysqlServer
  name: 'travel_db'
  properties: {
    charset: 'utf8'
    collation: 'utf8_general_ci'
  }
}

output RESOURCE_GROUP_ID string = resourceGroup().id
output appServiceUrl string = 'https://${appService.properties.defaultHostName}'
output appServiceName string = appService.name
output mysqlServerName string = mysqlServer.name
output keyVaultUrl string = keyVault.properties.vaultUri
