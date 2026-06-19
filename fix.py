import os

f1 = '/home/ubuntu/visionlab-ide/lib/vscode/src/vs/workbench/contrib/files/browser/explorerViewlet.ts'
with open(f1, 'r') as f:
    data = f.read()
data = data.replace("import { IViewsRegistry, IViewDescriptor, ViewContainer, IViewDescriptorService, ViewContentGroups } from 'vs/workbench/common/views';", "import { IViewsRegistry, IViewDescriptor, Extensions, ViewContainer, IViewDescriptorService, ViewContentGroups } from 'vs/workbench/common/views';")
with open(f1, 'w') as f:
    f.write(data)

f2 = '/home/ubuntu/visionlab-ide/lib/vscode/src/vs/workbench/contrib/welcomeGettingStarted/browser/gettingStarted.contribution.ts'
with open(f2, 'r') as f:
    data = f.read()
data = data.replace("registerWorkbenchContribution2(StartupPageRunnerContribution.ID, WorkbenchPhase.AfterRestored);", "// registerWorkbenchContribution2(StartupPageRunnerContribution.ID, StartupPageRunnerContribution, WorkbenchPhase.AfterRestored);")
with open(f2, 'w') as f:
    f.write(data)
