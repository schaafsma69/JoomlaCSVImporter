# JoomlaCSVImporter

## Purpose
Joomla CSV Importer is a Joomla Component that helps you import CSV data. 

This project is a work in progress and not ready for use.

## Approach
The approach is as follows:
- In the administrator you create tasks and each task has an import definition file. 
- Once you created the definition file, you can run the task. 
- The first thing it will do is ask for the source file (in CSV format, hence the name).
- Next the import will commence based on the definition of the task

## Format
Definition of a task is done using XML. The task definition in Joomla can be done directly or using a form that creates the proper XML for you.
