String gitCommitHash
String gitCommitAuthor
String gitCommitMessage

pipeline {
    agent {
            docker { image 'harehman/php:lib-compression-xdebug-with-git' }
        }
    environment {
        SLACK_NOTIFICATION_CHANNEL = "#team-core"
    }
    stages {
        stage('Prepare Info!') {
            steps {
                checkout scm
                sh 'printenv'
                script {
                    gitCommitHash = sh(returnStdout: true, script: 'git rev-parse --short HEAD').trim()
                    gitCommitAuthor = sh(returnStdout: true, script: 'git show -s --pretty=%aN').trim()
                    gitCommitMessage = sh(returnStdout: true, script: 'git show -s --pretty=%s').trim()
                }
            }
        }
        stage('Installing Dependencies') {
            steps {
                sh 'composer install --ignore-platform-reqs'
            }
        }
        stage('Running Tests') {
            steps {
                sh './vendor/bin/phpunit --testdox'
            }
        }
        stage('Running Coverage') {
            steps {
                sh './vendor/bin/phpunit --coverage-text=coverage.txt && cat coverage.txt'
            }
        }
        stage('Running PHPStan') {
            steps {
                sh './vendor/bin/phpstan analyze src/ --level 5'
            }
        }
    }
    post {
        failure {
            slackSend message: "lib-compression <${RUN_DISPLAY_URL}|failed>.", channel: "${SLACK_NOTIFICATION_CHANNEL}", color: "danger"
        }
        success {
            slackSend message: "*lib-compression <${RUN_DISPLAY_URL}|success>*. \nAll checks passed for *${GIT_BRANCH}* \nat commit *${gitCommitHash}* \nwhen *${gitCommitAuthor}* performed \n> ${gitCommitMessage}.", channel: "${SLACK_NOTIFICATION_CHANNEL}", color: "good"
        }
    }
}