<?php

namespace App\Enums;

enum Category: string
{
    case AiManagement = 'AI Management';
    case CloudManagement = 'Cloud Management';
    case CybersecurityGovernance = 'Cybersecurity Governance';
    case CybersecurityStrategy = 'Cybersecurity Strategy';
    case CybersecurityManagement = 'Cybersecurity Management';
    case CybersecurityRolesAndResponsibilities = 'Cybersecurity Roles and Responsibilities';
    case CybersecurityRiskManagement = 'Cybersecurity Risk Management';
    case CybersecurityInItProjectManagement = 'Cybersecurity in Information and Technology Project Management';
    case CybersecurityInHumanResources = 'Cybersecurity in Human Resources';
    case CybersecurityAwarenessAndTrainingProgram = 'Cybersecurity Awareness and Training Program';
    case AssetManagement = 'Asset Management';
    case IdentityAndAccessManagement = 'Identity and Access Management';
    case InformationSystemsAndProcessingFacilitiesProtection = 'Information Systems and Information Processing Facilities Protection';
    case AntiMalware = 'Anti-Malware';
    case StorageMediaManagement = 'Storage Media Management';
    case PatchManagement = 'Patch Management';
    case ClockSynchronization = 'Clock Synchronization';
    case EmailProtection = 'Email Protection';
    case NetworkSecurityManagement = 'Network Security Management';
    case MobileDevicesSecurity = 'Mobile Devices Security';
    case DataAndInformationProtection = 'Data and Information Protection';
    case Cryptography = 'Cryptography';
    case BackupAndRecoveryManagement = 'Backup and Recovery Management';
    case VulnerabilityManagement = 'Vulnerability Management';
    case PenetrationTesting = 'Penetration Testing';
    case CybersecurityEventLogsAndMonitoringManagement = 'Cybersecurity Event Logs and Monitoring Management';
    case ProblemManagement = 'Problem Management';
    case PhysicalSecurity = 'Physical Security';
    case SecurityArchitectureAndDesignReview = 'Security Architecture and Design Review';
    case ApplicationSecurity = 'Application Security';
    case BusinessContinuityManagement = 'Business Continuity Management';
    case ThirdPartyCybersecurityManagement = 'Third-party Cybersecurity Management';
    case AvailabilityManagement = 'Availability Management';
    case CapacityAndDemandManagement = 'Capacity and Demand Management';
    case ConfigurationManagement = 'Configuration Management';
    case ChangeAndReleaseManagement = 'Change and Release Management';
    case ForensicManagement = 'Forensic Management';
    case CyberInsuranceAndClaimsReadiness = 'Cyber Insurance and Claims Readiness';
    case PaymentSystems = 'Payment Systems';
    case ElectronicBanking = 'Electronic Banking';
}
