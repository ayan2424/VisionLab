import { CommandLineIcon, XMarkIcon } from "@heroicons/react/24/outline";
import { useContext, useEffect, useRef, useState } from "react";
import { CloseButton } from ".";
import { IdeMessengerContext } from "../context/IdeMessenger";
import useCopy from "../hooks/useCopy";
import { getPlatform } from "../util";
import { getLocalStorage, setLocalStorage } from "../util/localStorage";
import { CopyButton } from "./StyledMarkdownPreview/StepContainerPreToolbar/CopyButton";
import { RunInTerminalButton } from "./StyledMarkdownPreview/StepContainerPreToolbar/RunInTerminalButton";
import { Card } from "./ui";

interface CliInstallBannerProps {
  /** Number of sessions user has had - banner shows only if >= sessionThreshold */
  sessionCount?: number;
  /** Minimum sessions before showing banner (default: always show) */
  sessionThreshold?: number;
  /** If true, dismissal is permanent via localStorage (default: session only) */
  permanentDismissal?: boolean;
}

export function CliInstallBanner({
  sessionCount,
  sessionThreshold,
  permanentDismissal = false,
}: CliInstallBannerProps = {}) {
  const ideMessenger = useContext(IdeMessengerContext);
  const [cliInstalled, setCliInstalled] = useState<boolean | null>(null);
  const [dismissed, setDismissed] = useState(false);
  const commandTextRef = useRef<HTMLSpanElement>(null);
  const { copyText } = useCopy("npm i -g @continuedev/cli");
  const [showCopiedMessage, setShowCopiedMessage] = useState(false);

  // Suppress unused warnings
  ideMessenger; cliInstalled; setCliInstalled; dismissed; setDismissed;
  commandTextRef; copyText; showCopiedMessage; setShowCopiedMessage;

  const handleCommandClick = () => {
    // Select the text
    if (commandTextRef.current) {
      const selection = window.getSelection();
      const range = document.createRange();
      range.selectNodeContents(commandTextRef.current);
      selection?.removeAllRanges();
      selection?.addRange(range);
    }
    // Copy to clipboard
    copyText();

    // Show "Copied!" message for 3 seconds
    setShowCopiedMessage(true);
    setTimeout(() => setShowCopiedMessage(false), 3000);
  };

  useEffect(() => {
    // Check if user has permanently dismissed the banner
    if (permanentDismissal) {
      const hasDismissed = getLocalStorage("hasDismissedCliInstallBanner");
      if (hasDismissed) {
        setDismissed(true);
        return;
      }
    }

    const checkCliInstallation = async () => {
      try {
        const platform = getPlatform();
        // Use 'which' on mac/linux, 'where' on windows
        const command = platform === "windows" ? "where cn" : "which cn";

        const [stdout, stderr] = await ideMessenger.ide.subprocess(command);

        // If stdout has content (path to cn), it's installed
        // If empty or stderr has "not found", it's not installed
        const isInstalled =
          stdout.trim().length > 0 && !stderr.includes("not found");
        setCliInstalled(isInstalled);
      } catch (error) {
        // If subprocess throws an error, assume CLI is not installed
        setCliInstalled(false);
      }
    };

    void checkCliInstallation();
  }, [ideMessenger, permanentDismissal]);

  const handleDismiss = () => {
    setDismissed(true);
    if (permanentDismissal) {
      setLocalStorage("hasDismissedCliInstallBanner", true);
    }
  };

  // Don't show if:
  // - Still loading CLI status
  // - CLI is already installed
  // - User has dismissed it
  // - Session threshold not met (if threshold is set)
  if (
    cliInstalled === null ||
    cliInstalled === true ||
    dismissed ||
    (sessionThreshold !== undefined &&
      (sessionCount === undefined || sessionCount < sessionThreshold))
  ) {
    return null;
  }

  return null;
}
