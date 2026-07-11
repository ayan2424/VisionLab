$sourceIcon = "C:\Users\ayans\OneDrive\Pictures\tmp\code-icon.svg"
$remoteTmpIcon = "/tmp/code-icon.svg"
$remoteDir = "/home/ubuntu/code-server-fresh"
$sshKey = "C:\Users\ayans\OneDrive\Documents\A_Projects\Aptech\Vision2026\VisionLab\LightsailDefaultKey-ap-south-1.pem"

Write-Host "Uploading icon to server..."
scp -i $sshKey -o StrictHostKeyChecking=no $sourceIcon "ubuntu@15.207.144.48:$remoteTmpIcon"

Write-Host "Replacing all code-icon.svg files..."
ssh -i $sshKey -o StrictHostKeyChecking=no ubuntu@15.207.144.48 "find $remoteDir -type f -name 'code-icon.svg' -exec cp $remoteTmpIcon {} \; -print"

Write-Host "Done! Sab icons replace ho gaye hain."
