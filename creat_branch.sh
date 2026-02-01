#!/bin/bash

# 引数チェック
if [ -z "$1" ]; then
  echo "Error: Please specify a new branch name."
  echo "Usage: ./create_branch.sh <branch_name>"
  exit 1
fi

NEW_BRANCH=$1

# 現在のブランチを確認
CURRENT_BRANCH=$(git branch --show-current)

# mainブランチに切り替え
if [ "$CURRENT_BRANCH" != "main" ]; then
  echo "Switching to the main branch..."
  git checkout main
  if [ $? -ne 0 ]; then
    echo "Error: Failed to switch to the main branch."
    exit 1
  fi
fi

# mainブランチを最新状態に更新
echo "Pulling the latest changes from the main branch..."
git pull origin main
if [ $? -ne 0 ]; then
  echo "Error: Failed to pull the latest changes from the main branch."
  exit 1
fi

# 新しいブランチを作成して切り替え
echo "Creating and switching to the new branch '$NEW_BRANCH'..."
git checkout -b "$NEW_BRANCH"
if [ $? -ne 0 ]; then
  echo "Error: Failed to create the new branch."
  exit 1
fi

echo "The new branch '$NEW_BRANCH' has been created and switched to successfully."